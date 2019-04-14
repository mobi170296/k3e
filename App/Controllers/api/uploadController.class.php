<?php
    namespace App\Controllers\api;
    use Core\Controller;
    use Library\Database\Database;
    use App\Models\Authenticate;
    use App\Exception\AuthenticateException;
    use Library\Database\DBException;
    use Library\Image\ImageInfo;
    use Library\Image\ImageInfoException;
    use App\Models\ImageMapModel;
    
    use Library\File\ImageUploader;
    use Library\File\UploadImageException;
    
    class uploadController extends Controller{
        public function avatarimage(){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST() || !isset($this->files->avatar) || !is_array($this->files->avatar) || !is_string($this->files->avatar['name']) || $this->files->avatar['error']){
                $result->header->code = 1;
                $result->header->errors = ['Không tìm thấy tập tin đã upload'];
                return $this->View->RenderJson($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                $support = ['jpg', 'jpeg', 'png'];
                $imageinfo = new ImageInfo($this->files->avatar['tmp_name']);
                
                $extension = $imageinfo->getRealExtension();
                
                if(in_array($extension, $support)){
                    $uploader = new ImageUploader();
                    $uploader->upload($this->files->avatar['tmp_name'], $extension, PUBLIC_UPLOAD_IMAGE_DIR);
                    $imagemap = new ImageMapModel($database);
                    $imagemap->diskpath = $uploader->getDir() . DS . $uploader->getFileName();
                    $imagemap->mimetype = $imageinfo->getMimeType();
                    $imagemap->urlpath = str_replace(DS, '/', PUBLIC_UPLOAD_IMAGE_PATH . DS . $uploader->getAutoPath() . DS . $uploader->getFileName());
                    $imagemap->user_id = $user->id;
                    $imagemap->linked = ImageMapModel::LINKED;
                    
                    try{
                        $hasAvatar = $user->loadAvatar();
                        $database->startTransaction();
                        $imagemap->add();
                        $user->updateAvatarId($database->lastInsertId());
                        $result->header->code = 0;
                        $result->header->message = 'Uploaded';
                        $result->body = new \stdClass();
                        $result->body->data = new \stdClass();
                        $result->body->data->url = $imagemap->urlpath;
                        $database->commit();
                        if($hasAvatar){
                            unlink($user->avatar->diskpath);
                        }
                    } catch (DBException $ex) {
                        unlink($imagemap->diskpath);
                        $database->rollback();
                        throw $ex;
                    }
                }else{
                    $result->header->code = 1;
                    $result->header->errors = ['Format not supported'];
                }
                
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            } catch(AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            } catch(UploadImageException $e){
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsArray();
            } catch(ImageInfoException $e){
                $result->header->code = 1;
                $result->header->errors = [$e->getMessage()];
            }
            
            return $this->View->RenderJson($result);
        }
        public function productimage(){
            $result = new \stdClass();
            $result->header = new \stdClass();
            if(!$this->isPOST() || !is_array($this->files->image) || !is_string($this->files->image['name'])){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
                return $this->View->RenderJson($result);
            }
            
            try{
                $database = new Database();
                $user = (new Authenticate($database))->getUser();
                
                
                $uploader = new ImageUploader();
                $uploader->upload($this->files->image['tmp_name'], 'jpg', PUBLIC_UPLOAD_IMAGE_DIR);
                
                $result->header->code = 0;
                $result->header->message = 'OK You are success upload image to ' . $uploader->getDir() . DS . $uploader->getFileName();
                
                
//                if($user->isMerchant()){
//                    
//                }else{
//                    $result->header->code = 1;
//                    $result->header->errors = ['You must be merchant to upload ProductImage'];
//                }
            } catch (DBException $ex) {
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            } catch(AuthenticateException $e){
                $result->header->code = 1;
                $result->header->errors = ['invalid'];
            } catch(UploadImageException $e){
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsArray();
            }
            
            return $this->View->RenderJson($result);
        }
    }