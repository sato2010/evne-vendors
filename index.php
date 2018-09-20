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
    private $properties = array();

    public function __construct()
    {
        $this->add_new_roles();
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

        $this->getCapabilities();
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
        else
        if( isset($post['exist_name']) && !empty($post['exist_name']) ){
            $role = get_role( strtolower($post['exist_name']) );
            foreach ($this->capabilities as $index => $cap) {
                $role->remove_cap($cap);
            }
        }

        if(isset($post['cap']) && ($role)){
            foreach ($post['cap'] as $cup => $on) {
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

        ?>
        <script>
            function openTab(evt, tabName) {

                jQuery('.exist-roles').val('');

                jQuery('input[type="checkbox"]').each(
                    function (i,v) {
                        jQuery(v).prop('checked', false);
                    }
                );
                // Declare all variables
                var i, tabcontent, tablinks;

                // Get all elements with class="tabcontent" and hide them
                tabcontent = document.getElementsByClassName("evne-tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }

                // Get all elements with class="tablinks" and remove the class "active"
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }

                // Show the current tab, and add an "active" class to the button that opened the tab
                document.getElementById(tabName).style.display = "block";
                evt.currentTarget.className += " active";
            }


        </script>
        <div class='info-container'>
            <h2>Roles and capabilities</h2>
            <div class="main-content">

                <form class="form-basic" method="post" action="">
                    <input type="hidden" value="t1">
                    <div class="evne-tab">
                        <a class="tablinks active" onclick="openTab(event, 't1')">Add new role</a>
                        <a class="tablinks" onclick="openTab(event, 't2')">Edit role</a>
                        <a class="tablinks" onclick="openTab(event, 't3')">Edit user (comming soon)</a>
                    </div>
                    <div id="t1" class="evne-tabcontent active">
                        <div class="form-title-row">
                            <h1>Add new role</h1>
                        </div>
                        <div class="form-row">
                            <label>
                                <span>Role name</span>
                                <input type="text" name="new_name" id="new_name">
                            </label>
                        </div>
                    </div>


                    <div id="t2" class="evne-tabcontent">
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
                    </div>

                    <div class="row">
                        <?php $divider = round(count($this->capabilities) / 2); ?>
                        <?php foreach ($this->capabilities as $index => $cap): ?>
                            <?php if($index%$divider == 0): ?>
                                <div class="main-col">
                            <?php endif; ?>
                                <?php $checked = in_array($cap, $existCap) ? 'checked' : '' ; ?>
                                <div class="col-6">
                                    <span><?php echo ($index + 1) ?></span>
                                    <label>
                                        <input type="checkbox" name="cap[<?= $cap ?>]" <?= $checked ?> >
                                        <span><?= $cap ?></span>
                                    </label>
                                </div>
                            <?php if(($index + 1)%$divider == 0): ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if($index != count($this->capabilities)): ?>
                            <?php //echo '</div>'; ?>
                        <?php endif; ?>
                    </div>




                </form>
                <div class="form-row">
                    <button type="submit" class="button button-primary button-large">save</button>
                </div>
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
        wp_enqueue_style( 'evne-vendor-style', plugin_dir_url( __FILE__ ) . '/assets/css/style.css' );
        wp_enqueue_script( 'evne-vendor-main',  plugin_dir_url( __FILE__ ) . '/assets/js/main.js', array('jquery'),'', true );
    }

}


//add_action('after_setup_theme', 'add_new_roles');



$Vendor = new Vendor();
$Vendor->InitPlugin();


