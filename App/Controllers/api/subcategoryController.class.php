<?php
    namespace App\Controllers\api;
    use Core\Controller;
    use Library\Database\Database;
    use App\Models\UserModel;
    use Library\Database\DBException;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use App\Models\SubCategoryModel;
    use App\Models\MainCategoryModel;
    use App\Exception\InputException;
    
    class subcategoryController extends Controller{
       public function add(SubCategoryModel $subcategory){
            header('content-type: application/json');
            $result = new \stdClass();    
            $result->header = new \stdClass();
            if(!$this->isPOST()){
                $result->header->code = 1;
                $result->header->message = 'Invalid REQUEST';
                $result->header->errors = ['Request không hợp lệ!'];
                $result->body = new \stdClass();
                return $this->View->RenderJson($result);
            }
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $subcategory->setDatabase($database);
                    $subcategory->checkValidForName()->checkValidForLink()->checkValidForMainCategoryId();
                    if(!$subcategory->isValid()){
                        throw new InputException($subcategory->getErrorsMap());
                    }else{
                        $maincategory = new MainCategoryModel($database);
                        $maincategory->id = $subcategory->maincategory_id;
                        $maincategory->loadData();
                        
                        $database->startTransaction();
                        $subcategory->add();
                        $database->commit();
                        
                        $result->header->code = 0;
                        $result->header->message = 'Thêm danh mục phụ ' . $subcategory->name . ' vào danh mục chính ' . $maincategory->name .' thành công';
                        return $this->View->RenderJson($result);
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->errors = ['Bạn không có quyền thực hiện thao tác này'];
                    return $this->View->RenderJson($result);
                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
                return $this->View->RenderJson($result);
            } catch(AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['Bạn không có quyền thực hiện thao tác này'];
                return $this->View->RenderJson($result);
            } catch(InputException $e){
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsMap();
                return $this->View->RenderJson($result);
            } finally{
                $database->rollback();
                $database->close();
            }
        }
        public function update(SubCategoryModel $input, $id){
            header('content-type: application/json');
            $result = new \stdClass();    
            $result->header = new \stdClass();
            if(!$this->isPOST() || !is_numeric($id)){
                $result->header->code = 1;
                $result->header->message = 'Invalid REQUEST';
                $result->header->errors = ['Request không hợp lệ!'];
                return $this->View->RenderJson($result);
            }
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $input->setDatabase($database);
                    $input->checkValidForLink()->checkValidForName()->checkValidForMainCategoryId();
                    if($input->isValid()){
                        $subcategory = new SubCategoryModel($database);
                        $subcategory->id = $input->id;
                        if($subcategory->loadData()){
                            $subcategory->update($input);
                            $result->header->code = 0;
                            $result->header->message = 'Đã cập nhật thành công danh mục';
                        }else{
                            throw new InputException(['id'=>'Danh mục cần cập nhật ']);
                        }
                    }else{
                        throw new InputException($input->getErrorsMap());
                    }
                }else{
                    throw new AuthenticateException('Bạn không có quyền thực hiện thao tác này', -1);
                }
            } catch(InputException $e){
                $result->header->code = 1;
                $result->header->message = '';
                $result->header->errors = $e->getErrorsMap();
            }catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
                $result->body = new \stdClass();
            } catch(AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['Bạn không có quyền thực hiện thao tác này'];
                $result->body = new \stdClass();
            } catch(Exception $e){
                $result->header->code = 1;
                $result->header->errors = ['Error: ' . $e->getMessage()];
            } finally{
                $database->rollback();
                $database->close();
            }
            
            return $this->View->RenderJson($result);
        }
        public function delete(){
            
        }
    }