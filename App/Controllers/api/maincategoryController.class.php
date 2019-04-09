<?php
    namespace App\Controllers\api;
    use Core\Controller;
    use Library\Database\Database;
    use Library\Database\DBException;
    use App\Models\UserModel;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use App\Models\MainCategoryModel;
    use App\Exception\InputException;
    
    class maincategoryController extends Controller{
        public function add(MainCategoryModel $maincategory){
            header('content-type: application/json');
            if(!$this->isPOST()){
                $result = new \stdClass();
                $result->header = new \stdClass();
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
                    $maincategory->setDatabase($database);
                    $maincategory->checkValidForName()->checkValidForLink();
                    if(!$maincategory->isValid()){
                        $result = new \stdClass();
                        $result->header = new \stdClass();
                        $result->header->code = 1;
                        $result->header->message = 'Invalid Input';
                        foreach($maincategory->getErrorsMap() as $k => $v){
                            $result->header->errors[] = $v;
                        }
                        $result->body = new \stdClass();
                        return $this->View->RenderJson($result);
                    }else{
                        $maincategory->standardization();
                        $database->startTransaction();
                        $maincategory->add();
                        $database->commit();
                        
                        $result = new \stdClass();
                        $result->header = new \stdClass();
                        $result->header->code = 0;
                        $result->header->message = 'Thêm danh mục chính ' . $maincategory->name . ' thành công!';
                        $result->body = new \stdClass();
                        return $this->View->RenderJson($result);
                    }
                }else{
                    $result = new \stdClass();
                    $result->header = new \stdClass();
                    $result->header->code = 1;
                    $result->header->message = 'Bạn không có quyền thực hiện thao tác này';
                    $result->body = new \stdClass();
                    return $this->View->RenderJson($result);
                }
            } catch (DBException $ex) {
                $result = new \stdClass();
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->message = $ex->getMessage();
                $result->header->errors = [$ex->getMessage()];
                return $this->View->RenderJson($result);
            } catch(AuthenticateException $e){
                $result = new \stdClass();
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->message = 'Bạn không có quyền thực hiện thao tác này';
                $result->header->errors = [$result->header->message];
                return $this->View->RenderJson($result);
            } catch(Exception $e){
                $result = new \stdClass();
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->message = 'Error: ' . $e->getMessage();
                $result->header->errors = [$result->header->message];
                return $this->View->RenderJson($result);
            } finally{
                $database->rollback();
                $database->close();
            }
        }
        public function update(MainCategoryModel $input, $id){
            header('content-type: application/json');
            if($id === null || !is_numeric($id) || !$this->isPOST()){
                $result = new \stdClass();
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->message = 'Invalid REQUEST';
                $result->header->errors = ['Request không hợp lệ!'];
                $result->body = new \stdClass();
                return $this->View->RenderJson($result);
            }
            
            $result = new \stdClass();
            
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $maincategory = new MainCategoryModel($database);
                    $maincategory->id = $id;
                    if($maincategory->loadData()){
                        $input->setDatabase($database);
                        $input->checkValidForLink()->checkValidForName();
                        if($input->isValid()){
                            $input->standardization();
                            $maincategory->update($input);
                            $result->header = new \stdClass();
                            $result->header->message = 'Đã cập nhật thành công danh mục chính ' . $maincategory->name;
                            $result->header->code = 0;
                        }else{
                            throw new InputException($input->getErrorsMap());
                        }
                    }else{
                        throw new InputException(['id'=>'Danh mục này không tồn tại!']);
                    }
                }else{
                    throw new AuthenticateException('Invalid Privilege', -1);
                }
            } catch (DBException $ex) {
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->message = '';
                $result->header->errors = [$ex->getMessage()];
                $result->body = new \stdClass();
            } catch (AuthenticateException $e){
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->message = '';
                $result->header->errors = [$e->getMessage()];
            } catch (InputException $e){
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsMap();
            }
            
            return $this->View->RenderJson($result);
        }
        public function up($id){
            header('content-type: application/json');
            if($id === null || !is_numeric($id) || !$this->isPOST()){
                $result = new \stdClass();
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->message = 'Invalid REQUEST';
                $result->header->errors = ['Request không hợp lệ!'];
                $result->body = new \stdClass();
                return $this->View->RenderJson($result);
            }
            
            $result = new \stdClass();
            
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                $user = $authenticate->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $maincategory = new MainCategoryModel($database);
                    $maincategory->id = $id;
                    if($maincategory->loadData()){
                        $database->startTransaction();
                        $result->header = new \stdClass();
                        if($maincategory->moveUp()){
                            $database->commit();
                            $result->header->code = 0;
                            $result->header->message = 'Đã dời vị trí của ' . $maincategory->name . ' lên trên thành công!';
                        }else{
                            $result->header->code = 1;
                            $result->header->errors = ['Dời vị trí thất bại!'];
                        }
                    }else{
                        throw new InputException(['Danh mục không tồn tại']);
                    }
                }else{
                    throw new AuthenticateException('Invalid Privileges', -1);
                }
            } catch (DBException $ex) {
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
            } catch (InputException $e){
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsMap();
            } catch (AuthenticateException $e){
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->errors = ['Lỗi xác thực'];
            } finally{
                $database->rollback();
                $database->close();
            }
            
            return $this->View->RenderJson($result);
        }
        public function down($id){
            header('content-type: application/json');
            if($id === null || !is_numeric($id) || !$this->isPOST()){
                $result = new \stdClass();
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->message = 'Invalid REQUEST';
                $result->header->errors = ['Request không hợp lệ!'];
                $result->body = new \stdClass();
                return $this->View->RenderJson($result);
            }
            
            $result = new \stdClass();
            
            try{
                $database = new Database();
                $authenticate = new Authenticate($database);
                
                $user = $authenticate->getUser();
                
                if($user->haveRole(UserModel::ADMIN_ROLE)){
                    $maincategory = new MainCategoryModel($database);
                    $maincategory->id = $id;
                    
                    if($maincategory->loadData()){
                        $database->startTransaction();
                        if($maincategory->moveDown()){
                            $database->commit();
                            $result->header = new \stdClass();
                            $result->header->code = 0;
                            $result->header->message = 'Đã dời vị trí của danh mục ' . $maincategory->name . ' xuống dưới thành công!';
                        }else{
                            $result->header = new \stdClass();
                            $result->header->code = 1;
                            $result->header->errors = ['Dời vị trí thất bại'];
                        }
                    }else{
                        throw new InputException(['Danh mục không tồn tại']);
                    }
                }else{
                    throw new AuthenticateException('Invalid Privileges', -1);
                }
            } catch (DBException $ex) {
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->errors = [$ex->getMessage()];
            } catch(InputException $e){
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsMap();
            }catch(AuthenticateException $e){
                $result->header = new \stdClass();
                $result->header->code = 1;
                $result->header->errors = ['Lỗi xác thực'];
            }finally{
                $database->rollback();
                $database->close();
            }
            
            return $this->View->RenderJson($result);
        }
    }