<?php
    namespace App\Controllers\api;
    
    use Core\Controller;
    
    use Library\Common\Text;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\ProductModel;
    
    class searchController extends Controller{
        public function product($keyword = '', $mcids, $star = 0, $priceorder = 'no', $page = 1, $ipp = 10){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            try{
                if(is_numeric($page) && $page >= 1 && $page == (int)$page){
                    
                }else{
                    $page = 1;
                }
                
                if(is_numeric($ipp) && $ipp >= 1 && $ipp == (int)$ipp){
                    
                }else{
                    $ipp = 10;
                }
                
                if(!is_string($mcids)){
                    $mcids = null;
                }else{
                    $mcidarray = explode(',', $mcids);

                    foreach($mcidarray as $mcid){
                        if(!is_numeric($mcid)){
                            $mcids = null;
                            break;
                        }
                    }
                }

                if(!is_numeric($star) || $star < 1 || $star > 5){
                    $star = 0;
                }

                if(!is_string($priceorder)){
                    $priceorder = 'no';
                }else{
                    if($priceorder != 'desc' && $priceorder != 'asc'){
                        $priceorder = 'no';
                    }
                }

                $database = new Database();

                //sql cho star filter
                $star_filter = "select product.id product_id, product.price product_price from product join orderitem on orderitem.product_id = product.id join assessment on assessment.product_id = orderitem.product_id && assessment.order_id = orderitem.order_id group by product.id having avg(assessment.starpoint) >= $star";


                //sql cho maincategory filter
                $mc_filter = "select product.id product_id, product.price product_price from product join subcategory on product.subcategory_id = subcategory.id join maincategory on subcategory.maincategory_id = maincategory.id where maincategory.id in ($mcids)";


                //sql cho keyword filter
                $names = Text::getWords($keyword);
                $namewheres = [];
                foreach($names as $name){
                    $name = $database->escape($name);
                    $namewheres[] = "product.name like '%$name%'";
                }
                $namewhere = implode(' and ', $namewheres);
                $keyword_filter = "select product.id product_id, product.price product_price from product where $namewhere";

                #truy van lay id
                $sql = "select keyword.product_id product_id from ($keyword_filter) as keyword";

                if($star != 0){
                    $sql .= " join ($star_filter) as star on star.product_id = keyword.product_id";
                }

                if($mcids != null){
                    $sql .= " join ($mc_filter) as mc on mc.product_id = keyword.product_id";
                }
                
                if($priceorder == 'desc'){
                    $sql .= " order by keyword.product_price desc";
                }
                
                if($priceorder == 'asc'){
                    $sql .= " order by keyword.product_price asc";
                }
                
                $sql .= ' limit ' . (($page - 1) * $ipp) . ',' . $ipp;

                #truy van lay total
                $totalsql = "select count(*) total from ($keyword_filter) as keyword";

                if($star != 0){
                    $totalsql .= " join ($star_filter) as star on star.product_id = keyword.product_id";
                }

                if($mcids != null){
                    $totalsql .= " join ($mc_filter) as mc on mc.product_id = keyword.product_id";
                }

                $rows = $database->query($sql);

                
                $totalrows = $database->query($totalsql);
                $total = $totalrows[0]->total;

                $products = [];
                
                foreach($rows as $row){
                    $product = new ProductModel($database);
                    $product->id = $row->product_id;
                    $product->loadData();
                    $product->loadMainImage();
                    $product->loadWard();
                    $product->ward->loadDistrict();
                    $product->ward->district->loadProvince();
                    
                    $jproduct = new \stdClass();
                    $jproduct->link = $product->getProductLink();
                    $jproduct->name = $product->name;
                    $jproduct->mainimageurl = $product->getMainImageThumbnail();
                    $jproduct->price = (int)$product->price;
                    $jproduct->old_price = (int)$product->original_price;
                    $jproduct->starpercent = $product->getStarRatingPoint() / 5 * 100;
                    $jproduct->provincename = $product->ward->district->province->name;
                    
                    $products[] = $jproduct;
                }
                
                
                $result->header->code = 0;
                $result->header->message = 'OK';
                $result->body = new \stdClass();
                $result->body->total = (int)$total;
                $result->body->products = $products;
                return $this->View->RenderJSON($result);
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->message = 'DBERR';
                return $this->View->RenderJSON($result);
            }
            
        }
    }