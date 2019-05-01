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
    use Library\Image\ImageResizer;
    use App\Exception\InputException;
    use Library\Image\ImageResizerException;
    
    class uploadController extends Controller{
        public function test(){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            $uploader = new ImageUploader();
            $imageinfo = new ImageInfo($this->files->image['tmp_name']);
            $extension = $imageinfo->getRealExtension();
            
            $uploader->upload($this->files->image['tmp_name'], $extension, PUBLIC_UPLOAD_IMAGE_DIR);
            
            $database = new Database();
            
            $user = (new Authenticate($database))->getUser();
            
            $imagemap = new ImageMapModel($database);
            $imagemap->diskpath = $uploader->getDir() . DS . $uploader->getFileName();
            
            $imageresizer = new ImageResizer($imagemap->diskpath);
            
            $imageresizer->containResize(400, 400);
            
            $imagemap->linked = ImageMapModel::UNLINKED;
            
            $imagemap->urlpath = str_replace(DS, '/', PUBLIC_UPLOAD_IMAGE_PATH . DS . $uploader->getAutoPath() . DS . $uploader->getFileName());
            
            $imagemap->mimetype = $imageinfo->getMimeType();
            
            $imagemap->user_id = $user->id;
            
            $imagemap->add();
            
            $result->header->code = 0;
            $result->body = new \stdClass();
            $result->body->data = new \stdClass();
            $result->body->data->url = $imagemap->urlpath;
            $result->body->data->id = $database->lastInsertId();
            
            return $this->View->RenderJson($result);
        }
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
                    
                    #resize image 
                    $resizer = new ImageResizer($imagemap->diskpath);
                    $resizer->coverResize(300, 300);
                    
                    try{
                        $hasAvatar = $user->loadAvatar();
                        $database->startTransaction();
                        $imagemap->add();
                        $lastinsertid = $database->lastInsertId();
                        $oldimagemap = new ImageMapModel($database);
                        $oldimagemap->id = $user->avatar_id;
                        $oldimagemap->delete();
                        $user->updateAvatarId($lastinsertid);
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
            } catch(ImageResizerException $e){
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
                if($user->loadShop()){
                    if($this->files->image['error'] != UPLOAD_ERR_OK){
                        throw new InputException(['Tập tin upload không thành công']);
                    }
                    $imageinfo = new ImageInfo($this->files->image['tmp_name']);
                    $ext = $imageinfo->getRealExtension();
                    $width = $imageinfo->getSize()->width;
                    $height = $imageinfo->getSize()->height;
                    
                    $width = min($width, $height);
                    
                    $uploader = new ImageUploader();
                    $uploader->upload($this->files->image['tmp_name'], $ext, PUBLIC_UPLOAD_IMAGE_DIR);
                    
                    $imageresizer = new ImageResizer($uploader->getDir() . DS . $uploader->getFileName());
                    $imageresizer->coverResize($width, $width);
                    
                    $imagemap = new ImageMapModel($database);
                    $imagemap->diskpath = PUBLIC_UPLOAD_IMAGE_DIR . DS . $uploader->getAutoPath() . DS . $uploader->getFileName();
                    $imagemap->linked = ImageMapModel::UNLINKED;
                    $imagemap->mimetype = $imageinfo->getMimeType();
                    $imagemap->user_id = $user->id;
                    $imagemap->urlpath = str_replace(DS, '/', PUBLIC_UPLOAD_IMAGE_PATH . DS . $uploader->getAutoPath() . DS . $uploader->getFileName());
                    $imagemap->add();
                    $result->header->code = 0;
                    $result->header->message = 'Đã upload thành công ảnh';
                    $result->body = new \stdClass();
                    $result->body->data = new \stdClass();
                    $result->body->data->url = $imagemap->urlpath;
                    $result->body->data->id = $database->lastInsertId();
                }else{
                    $result->header->code = 1;
                    $result->header->errors = ['Bạn không có cửa hàng'];
                    $result->header->errors = ['Bạn không có cửa hàng'];
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
            } catch(InputException $e){
                $result->header->code = 1;
                $result->header->errors = $e->getErrorsMap();
            } catch(ImageInfoException $e){
                $result->header->code = 1;
                $result->header->errors = [$e->getMessage()];
            } catch(ImageResizerException $e){
                $result->header->code = 1;
                $result->header->errors = [$e->getMessage()];
            }
            
            return $this->View->RenderJson($result);
        }
    }