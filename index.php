<?php
/*
Plugin Name: evne-vendors
Plugin URI: http://evnedev.com
Description: evne-vendors
Version: 1.0
Author: evne
Author URI: http://evnedev.com
*/

class Vendor{

    private $capabilities;
    private $roles = [];

    public function __construct()
    {

    }

    private function getRoles(){
        global $wp_roles;
        $this->roles = $wp_roles->role_names;
        unset($this->roles['administrator']);
        unset($this->roles['editor']);
        unset($this->roles['author']);
        unset($this->roles['contributor']);
        unset($this->roles['subscriber']);
        unset($this->roles['customer']);
        unset($this->roles['shop_manager']);
    }

    function add_new_roles()
    {
        $role = get_role( 'vendor' );
        // add a new capability
        if(!$role) {
            $result = add_role(
                'vendor',
                __('Vendor'),
                array(
                    'read' => true,  // true allows this capability
                    'edit_posts' => true,
                    'delete_posts' => false, // Use false to explicitly deny
                    'manage_woocommerce' => true,
                    'edit_products' => true,
                    'edit_product' => true,
                    'read_product' => true,
                    'delete_product' => true,
                    'edit_shop_order' => true,
                    'read_shop_order' => true,
                    //'edit_others_products' => true,
                )
            );

            if (null !== $result) {
                echo 'Yay! New role created!';
            } else {
                echo 'Oh... the basic_contributor role already exists.';
            }
        }

        ///$role->add_cap('manage_woocommerce', true);  // only woocomerce settings
        $role->add_cap('edit_products', true);
        $role->add_cap('edit_product', true);
        $role->add_cap('read_product', true);
        $role->add_cap('delete_product', true);
        $role->add_cap('edit_shop_order', true);
        $role->add_cap('read_shop_order', true);
        /*$role->add_cap('read_private_product', false);
        $role->add_cap('read_private_products', false);
        $role->add_cap('edit_other_products', false);
        $role->add_cap('edit_published_products', false);*/
        $role->add_cap('edit_published_products', true);

    }

    function PluginMenu()
    {
        $my_plugin_screen_name = add_menu_page(
            'Vendors',
            'Vendors',
            'manage_options',
            __FILE__,
            array($this, 'RenderPage'),
            plugins_url('/img/icon.png',__DIR__)
        );
    }

    public function saveParam($post){

        $newCap = [];
        $role = '';
        foreach ($this->capabilities as $index => $cap){
            $newCap[] = [$cap => false];
        }

        if(isset($post['new_name']) && !empty($post['new_name'])){
            $result = add_role(
                $post['new_name'],
                __($post['new_name']),
                $newCap

            );
            $role = get_role( $post['new_name'] );
        }

        if(isset($post['exist_name']) && !empty($post['exist_name'])){
            $role = get_role( strtolower($post['exist_name']) );
            foreach ($this->capabilities as $index => $cap) {
                var_dump($cap);
                $role->add_cap($cap, false);
            }
        }

        //var_dump($post, $role);die();
        if(isset($post['cap']) && ($role)){
            foreach ($post['cap'] as $cup => $on) {
                var_dump($cup);
                $role->add_cap($cup, true);
            }
        }
    }

    private function getExistCapabilities($name){
        return array_keys(get_role( strtolower($name) )->capabilities);
    }

    public function RenderPage(){
        global $wp_roles;
        $this->getRoles();
        $this->assetRegister();
        if(isset($_POST) && !empty($_POST)){
            $this->saveParam($_POST);
        }
        $this->getCapabilities();

        if(isset($_GET['name'])) {
            $existCap = $this->getExistCapabilities($_GET['name']);
        }
//var_dump($this->roles);
        ?>
        <div class='wrap'>
            <h2>Add new role</h2>
            <div class="main-content">

                <form class="form-basic" method="post" action="">

                    <div class="form-title-row">
                        <h1>Add new role</h1>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="button button-primary button-large">Submit Form</button>
                    </div>
                    <br/>
                    <div class="form-row">
                        <label>
                            <span>Role name</span>
                            <input type="text" name="new_name" id="new_name">
                        </label>
                    </div>

                    <div class="form-title-row">
                        <h1>Edit role</h1>
                    </div>
                    <select class="exist-roles" name="exist_name">
                        <?php if(isset($_GET['name'])): ?>
                            <option><?= $_GET['name'] ?></option>
                            <option></option>
                            <?php foreach ($this->roles as $index => $role): ?>
                                <option><?= $role ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option></option>
                            <?php foreach ($this->roles as $index => $role): ?>
                                <option><?= $role ?></option>
                            <?php endforeach; ?>
                        <?php endif;?>
                    </select>


                    <div class="row">

                        <?php foreach ($this->capabilities as $index => $cap): ?>
                            <?php $checked = in_array($cap, $existCap) ? 'checked' : '' ; ?>
                            <div class="col-6">
                                <label>
                                    <input type="checkbox" name="cap[<?= $cap ?>]" <?= $checked ?> >
                                    <span><?= $cap ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>


                    <div class="form-row">
                        <button type="submit">Submit Form</button>
                    </div>

                </form>

            </div>

        </div>
        <?php
    }

    public function InitPlugin()
    {
        add_action('admin_menu', array($this, 'PluginMenu'));
    }

    public function getCapabilities(){
        $this->capabilities = array_keys(get_role( 'administrator' )->capabilities);
    }

    public function assetRegister(){
        wp_enqueue_style( 'evne-vendor-style', plugin_dir_url( __FILE__ ) . '/assets/style.css', array( 'evne-style' ) );
        wp_enqueue_script( 'evne-vendor-main',  plugin_dir_url( __FILE__ ) . '/assets/js/main.js', array('jquery'),'', true );
    }

}


//add_action('after_setup_theme', 'add_new_roles');



$Vendor = new Vendor();
$Vendor->InitPlugin();


