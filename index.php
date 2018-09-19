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

    public function __construct()
    {
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

    function getRoles(){
        global $wp_roles;
        //echo '<pre>';
        var_dump($wp_roles->role_names);
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
        $this->getRoles();

    }

    public function saveParam($post){
        var_dump($post);
    }

    public function RenderPage(){
        $this->assetRegister();
        if(isset($_POST) && !empty($_POST)){
            $this->saveParam($_POST);
        }
        $this->getCapabilities();
        ?>
        <div class='wrap'>
            <h2>Add new role</h2>
            <div class="main-content">

                <form class="form-basic" method="post" action="">

                    <div class="form-title-row">
                        <h1>Add new role</h1>
                    </div>
                    <div class="form-row">
                        <button type="submit">Submit Form</button>
                    </div>
                    <div class="form-row">
                        <label>
                            <span>Role name</span>
                            <input type="text" name="name" id="cap_name">
                        </label>
                    </div>
                    <div class="row">
                        <?php foreach ($this->capabilities as $index => $cap): ?>
                            <div class="col-6">
                                <label>
                                    <input type="checkbox" name="cap[<?= $cap ?>]">
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


