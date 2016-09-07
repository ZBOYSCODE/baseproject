<?php
    namespace Gabs\Controllers;

    use Gabs\Models\Roles;
    use Gabs\Models\Permiso;

    
    class PermissionsController extends ControllerBase
    {
        public function initialize()
        {
            $this->_themeArray = array('topMenu'=>true, 'menuSel'=>'','pcView'=>'', 'pcData'=>'', 'jsScript'=>'');
        }
        
        /**
         * Default action, shows the search form
         */
        public function indexAction()
        {   
            $data['permissions'] = $this->getControllersAndMethod();

            $data['roles'] = Roles::find();

            $themeArray = $this->_themeArray;
            $themeArray['pcView']   = 'permissions/list';
            $themeArray['addJs'][]  = "js/permissions.js";

            $themeArray['pcData'] = $data;
            echo $this->view->render('theme', $themeArray);
        }

        public function getPermissionsAction()
        {
            $rol = $this->request->getPost("rol", 'int');

            $data['estado'] = true;
            $permissions = Permiso::findByRolId($rol);


            foreach ($permissions as $permission) {

                $data['permissions'][] = $permission->permission;
            }

            echo json_encode($data);
        }

        public function updatePermissionsAction()
        {
            $rol        =  $this->request->getPost("rol", 'int');
            $permissions   =  $this->request->getPost("permissions");

            $data['estado'] = true;

            $this->deletePermissions($rol);

            if(count($permissions) > 0){
                foreach ($permissions as $permission) {

                    $per = new Permiso();
                    $per->permission   = $permission;
                    $per->rol_id    = $rol;

                    if(!$per->save()){
                        $data['estado'] = false;
                        $data['msg']    = "Error al guardar permissions.";

                        foreach ($per->getMessages() as $message) {
                            $data['detalle'][] = $message->getMessage();
                        }
                    } 
                }

                if($data['estado']){
                    $data['msg']    = "Permissions actualizados correctamente.";
                }
            } else {
                $data['msg']    = "No hay permissions para actualizar.";
            }

                
                

            echo json_encode($data);
        }

        private function getControllersAndMethod()
        {   

            $controladores = $this->getControllers();

            foreach ($controladores as $className) {

                require_once($className.'.php');

                $a = '\Gabs\Controllers\\'.$className;

                if(class_exists($a)) {

                    $meths = get_class_methods( new $a );

                    foreach ($meths as $meth) {

                        $pos = strpos($meth, 'Action');

                        if($pos !== false) {
                            $arr[str_replace('Controller', '', $className)][] = str_replace('Action', '', $meth) ;
                        }
                    }
                }    
            }

            return $arr;
        }


        private function getControllers()
        {

            $dir = $this->config->application->controllersDir;
            $ctrls =  scandir($dir);

            foreach ($ctrls as $controlador) {
                
                $ruta_controlador = $dir.$controlador;

                if(is_file($ruta_controlador))
                {
                    // Obtenemos el nombre de nuestro controlador
                    $namectrl = str_replace(".php", "", $controlador);

                    if($namectrl != "ControllerBase" )
                    {
                        $controladores[] = $namectrl;
                    }
                    
                }
            }

            return $controladores;
        }

        private function deletePermissions($rol)
        {
            $permissions = Permiso::findByRolId($rol);

            foreach ($permissions as $permission){
                $permission->delete();
            }
        }
    }