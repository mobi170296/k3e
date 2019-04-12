<?php
    namespace App\Controllers\api;
    use Core\Controller;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\ProvinceList;
    use App\Models\DistrictList;
    use App\Models\DistrictModel;
    use App\Models\WardList;
    use App\Models\ProvinceModel;
    
    
    class addressController extends Controller{
        public function getallprovince(){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST()){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
                return $this->View->RenderJson($result);
            }
            
            try{
                $database = new Database();
                $list = (new ProvinceList($database))->getAll();
                if(count($list)==0){
                    $result->header->code = 1;
                    $result->header->errors = ['Danh sách tỉnh thành rỗng'];
                    return $this->View->RenderJson($result);
                }
                $result->header->code = 0;
                $result->header->message = '';
                $result->body = new \stdClass();
                foreach($list as $province){
                    $o = new \stdClass();
                    $o->id = $province->id;
                    $o->name = $province->name;
                    $result->body->data[] = $o;
                }
                return $this->View->RenderJson($result);
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
                return $this->View->RenderJson($result);
            }
        }
        
        public function getdistrictfromprovince($province_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST() || !is_numeric($province_id)){
                $result->header ->code = 1;
                $result->header->errors = ['invalid'];
                return $this->View->RenderJson($result);
            }
            
            try{
                $database = new Database();
                $province = new ProvinceModel($database);
                $province->id = $province_id;
                if(!$province->loadData()){
                    $result->header->code = 1;
                    $result->header->errors = ['Không tồn tại tỉnh thành phố'];
                    return $this->View->RenderJson($result);
                }
                $province->loadDistricts();
                $result->header->code = 0;
                $result->header->message = '';
                $result->body = new \stdClass();
                foreach($province->districts as $district){
                    $o = new \stdClass();
                    $o->id = $district->id;
                    $o->name = $district->name;
                    $result->body->data[] = $o;
                }
                return $this->View->RenderJson($result);
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
                return $this->View->RenderJson($result);
            }
        }
        
        public function getwardfromdistrict($district_id){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST() || !is_numeric($district_id)){
                $result->header ->code = 1;
                $result->header->errors = ['invalid'];
                return $this->View->RenderJson($result);
            }
            
            try{
                $database = new Database();
                $district = new DistrictModel($database);
                $district->id = $district_id;
                if(!$district->loadData()){
                    $result->header->code = 1;
                    $result->header->errors = ['Không tồn tại quận huyện này'];
                    return $this->View->RenderJson($result);
                }
                $district->loadWards();
                $result->header->code = 0;
                $result->header->message = '';
                $result->body = new \stdClass();
                foreach($district->wards as $ward){
                    $o = new \stdClass();
                    $o->name = $ward->name;
                    $o->id = $ward->id;
                    $o->code = $ward->code;
                    $o->district_id = $ward->district_id;
                    $result->body->data[] = $o;
                }
                return $this->View->RenderJson($result);
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
                return $this->View->RenderJson($result);
            }
        }
    }