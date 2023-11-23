<?php
/*
Plugin Name: باشگاه مشتریان 
Description: سیستم مدیریت باشگاه مشتریان
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function admin_styles($hook) {
    if( $hook != 'toplevel_page_club' && $hook != 'customer-club_page_gifts_setting' && $hook != 'customer-club_page_list_gifts_setting') {
        return;
    }
    /* styles admin*/
    wp_enqueue_style( 'style', plugins_url('club/admin/style.css'), array(), '1.0.0','screen');

    wp_enqueue_media();

    /* scripts admin*/
    wp_enqueue_script('main', plugins_url('/admin/main.js', __FILE__), array('jquery'), false, true);
    wp_localize_script( "main" , "ajaxclubplugin" , array('ajax_url' => admin_url("admin-ajax.php" ) ) );
}
add_action('admin_enqueue_scripts', 'admin_styles');

function enqueue_scripts_styles() {
    /* styles */
    wp_enqueue_style( 'style', plugins_url('/assets/style.css', __FILE__), array(), '1.0.0','screen');
        
    /* scripts */
    wp_enqueue_script('main', plugins_url('/assets/main.js', __FILE__), array('jquery'), false, true);
    wp_localize_script( "main" , "ajaxclubplugin" , array('ajax_url' => admin_url("admin-ajax.php" ) ) );
}
add_action('wp_enqueue_scripts', 'enqueue_scripts_styles');

include("admin/admin.php");
$options = get_option("club_api_settings"); 
if ( ! function_exists( 'club_plugin' ) ){

    function club_plugin(){

        global $wpdb;
    
        $charset_collate = "";

        if ( !empty( $wpdb->charset ) )
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} ";
    
        if ( !empty( $wpdb->collate ) )
            $charset_collate .= "COLLATE {$wpdb->collate}";
    
            $create_table_SQL_received = "CREATE TABLE `" . $wpdb->prefix . "received_club` ( 
                `id` INT NOT NULL AUTO_INCREMENT , 
                `userid` INT NOT NULL , 
                `code` VARCHAR(191) NOT NULL,
                `type_form` INT NOT NULL,
                `coupon` VARCHAR(191) NOT NULL , 
                `score` BIGINT NOT NULL , 
                `insert_score` BIGINT NOT NULL , 
                `dec_received` TEXT NOT NULL , 
                `date_received` BIGINT NOT NULL ,
                `type_received` INT NOT NULL , 
                PRIMARY KEY (`ID`)
            ) $charset_collate ENGINE = InnoDB;";

            $create_table_SQL_gift = "CREATE TABLE `" . $wpdb->prefix . "gift_club` ( 
                `id` INT NOT NULL AUTO_INCREMENT , 
                `title` VARCHAR(191) NOT NULL , 
                `score` BIGINT NOT NULL , 
                `text` TEXT NOT NULL , 
                `image` VARCHAR(191) NOT NULL , 
                `type` INT NOT NULL , 
                `dec_custom` TEXT NOT NULL , 
                `type_coupon` VARCHAR(191) NOT NULL , 
                `value_type_coupon` BIGINT NOT NULL , 
                `free_shipping` VARCHAR(191) NOT NULL , 
                `min_cart` BIGINT NOT NULL , 
                `max_cart` BIGINT NOT NULL , 
                `individual_use` VARCHAR(191) NOT NULL , 
                `exclude_sale_items` VARCHAR(191) NOT NULL , 
                `product_ids` VARCHAR(191) NOT NULL , 
                `exclude_product_ids` VARCHAR(191) NOT NULL , 
                `product_categories` VARCHAR(191) NOT NULL , 
                `exclude_product_categories` VARCHAR(191) NOT NULL ,
                `count_received` BIGINT NOT NULL ,
                PRIMARY KEY (`ID`)
            ) $charset_collate ENGINE = InnoDB;";

            $results = $wpdb->get_results( "ALTER TABLE `{$wpdb->prefix}users` ADD `count_club` TEXT NOT NULL DEFAULT '0' AFTER `display_name`");
            require( ABSPATH . "/wp-admin/includes/upgrade.php" );
            dbDelta( $create_table_SQL_user );
            dbDelta( $create_table_SQL_received );
            dbDelta( $create_table_SQL_gift );
    }

}

register_activation_hook( __FILE__, 'club_plugin' );

if ( ! function_exists( 'formClub' ) ){

    add_shortcode('addcode', 'formClub');
    function formClub(){
        ob_start();
        global $wpdb, $current_user;
        return '<div class="main-club"><form action="" method="POST" class="form-club">
            <input type="text" class="inputcodeclub" name="code" placeholder="کد معرف">
            <input type="button" value="ثبت" name="btnaddclub" class="btn_add_code_club">
        </form>
        </div>';
        return ob_get_clean();
    }
}
add_action("wp_ajax_render_form_code_club" , "render_form_code_club");
add_action("wp_ajax_nopriv_render_form_code_club" , "render_form_code_club");
if ( ! function_exists( 'render_form_code_club' ) ){
    function render_form_code_club(){

        global $wpdb, $current_user;

        if( is_user_logged_in() ){ 
            $usertrue = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "received_club` WHERE userid = '".$current_user->ID."' AND `type_form` = 0 ;" );
            if ($usertrue){
                echo json_encode(array('text' => 'شما کد معرف ثبت کرده اید', 'error' => true));
                die;
            }else{
                $userid = $current_user->ID;
                $username = $current_user->user_login;
                $new_username = "DsAs".strrev($username);
                $code = $_POST['code'];
                $ifcode = substr($code, 4);                    
                $ifcode = strrev($ifcode);
                $codetrue = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "users` WHERE user_login = '". $ifcode ."' ;" );
            
                if (!$codetrue){
                    echo json_encode(array('text' => 'کد معرف نامعتبر است', 'error' => true));
                    die;
                }
                elseif ($new_username == $code) {
                    echo json_encode(array('text' => 'شما کد معرف خود را وارد کرده اید', 'error' => true));
                    die;
                }
                else{
                    $friend_score = get_option( 'filde_friend_score' );
                    $user_score = get_option( 'filde_user_score' );
                    date_default_timezone_set('Asia/Tehran'); 
                    $date = time();
                    $wpdb->get_results( "INSERT INTO `" . $wpdb->prefix . "received_club` ( userid, code , score, insert_score, type_form, date_received) VALUES ( '".$userid."', '".$code."','".$user_score."','".$friend_score."', '0', '".$date."' )" );
                    $code = substr($code, 4);                    
                    $code = strrev($code);
                    $num = $wpdb->get_results( "SELECT `count_club` FROM `" . $wpdb->prefix . "users` WHERE `user_login` = '{$code}'" );
                    $num = $num[0]->count_club + $user_score;
                    $wpdb->get_results( "UPDATE `" . $wpdb->prefix . "users` SET `count_club` = '".$num."' WHERE `" . $wpdb->prefix . "users`.`user_login` = '{$code}'" );
                    $num = $wpdb->get_results( "SELECT `count_club` FROM `" . $wpdb->prefix . "users` WHERE `user_login` = '{$username}'" );
                    $num = $num[0]->count_club + $friend_score;
                    $wpdb->get_results( "UPDATE `" . $wpdb->prefix . "users` SET `count_club` = '".$num."' WHERE `" . $wpdb->prefix . "users`.`user_login` = '{$username}'" );
                    echo json_encode(array('text' => 'کد معرف دوست شما با موفقیت ثبت شد', 'error' => false, 'successtext' => "ثبت شد"));
                    die;
                }
            }
        }
        else{
            echo json_encode(array('text' => 'برای ثبت کد ابتدا وارد شوید', 'error' => true));
            die;
        }
    }
}


if ( ! function_exists( 'code_club' ) ){

    add_shortcode('mycode', 'code_club');
    function code_club(){
        if( is_user_logged_in() ){ 
            global $current_user;

            $username = $current_user->user_login;
            $code_club = '<span class="user_code_club">DsAs'.strrev($username).'</span>';
            return $code_club;
        }else{
            $code_club = '<span class="user_code_club">لطفا وارد شوید</span>';
            return $code_club;
        }
    }
}

if ( ! function_exists( 'score_history_club' ) ){

    add_shortcode('score_history_club', 'score_history_club');
    function score_history_club(){
        ob_start();
        if( is_user_logged_in() ){ ?>
        <div class="box_table_history_club">
            <?php
            global $wpdb, $current_user;

            $username = $current_user->user_login;
            $new_username = "DsAs".strrev($username);
            $results = $wpdb->get_results( "SELECT * FROM `". $wpdb->prefix ."received_club` WHERE `userid` = '".$current_user->ID."' Or `code` = '".$new_username."' ORDER BY `id` DESC;" );
            if($results){?>
            <table class="table_history_club">
                <thead>
                    <tr>
                        <td class="type_history_club">نوع</td>
                        <td class="date_history_club">تاریخ</td>
                        <td class="score_history_club">امتیاز</td>
                        <td class="dec_history_club">توضیحات</td>
                    </tr>
                </thead>
                <tbody class="tbody_history_club">
                    <?php foreach ($results as $key => $value) {?>
                    <tr>
                        <td class="tbody_type_history_club"><div><?php if($value->type_form == 0){
                            echo 'کد معرف';
                        }else{
                            echo 'هدیه';
                        }?></div></td>
                        <td class="tbody_date_history_club"><div><?php
                            date_default_timezone_set('Asia/Tehran'); 
                            $timestamp = $value->date_received ;
                            $date = date("H:i Y/m/d", $timestamp);
                            echo $date; 
                        ?></div></td>
                        <td class="tbody_score_history_club"><div>
                        <?php
                            if($value->userid == $current_user->ID && $value->type_form == 0){
                                echo '<span class="add_score_history">'.$value->insert_score.'</span>';
                            }elseif($value->code == $new_username && $value->type_form == 0){
                                echo '<span class="add_score_history">'.$value->score.'</span>';
                            }elseif($value->type_form == 1){
                                echo '<span class="remove_score_history">'.$value->score.'</span>';
                            }
                        ?>
                        </div></td>
                        <td class="tbody_dec_history_club"><div>
                        <?php 
                            if($value->userid == $current_user->ID && $value->type_form == 0){
                                echo "ثبت کد معرف دوستان توسط شما";
                            }elseif($value->code == $new_username && $value->type_form == 0){
                                echo "ثبت کد معرف شما توسط دوستان";
                            }elseif($value->type_form == 1){
                                if($value->type_received == 0){
                                    echo "کدتخفیف شما ".$value->coupon."<br>".$value->dec_received;
                                }else{
                                    echo $value->dec_received;
                                }
                            }
                        ?>
                        </div></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            <?php }else{?>
                <div class="empty_club">
                    <p>تاریخچه امتیازات شما خالی است!</p>
                </div>
            <?php } ?>
        </div>
        <?php }else{ ?>
            <div class="is_login_user_club">
                <p>برای مشاهده تاریخچه امتیازات و هدایا لطفا وارد شوید</p>
            </div>
        <?php }
        return ob_get_clean();
    }
}

if ( ! function_exists( 'count_code_club' ) ){

    add_shortcode('countcode', 'count_code_club');
    function count_code_club(){
        if( is_user_logged_in() ){ 

            global $wpdb, $current_user;

            $username = $current_user->user_login;
            $results = $wpdb->get_results( "SELECT `count_club` FROM `".$wpdb->prefix."users` WHERE user_login = '".$username."';" );
            return $results[0]->count_club;
        }
    }
}

if ( ! function_exists( 'top_user_club' ) ){

    add_shortcode('topuserclub', 'top_user_club');
    
    function top_user_club(){
        ob_start();
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}users` ORDER BY `count_club` ASC limit 5;" );?>
            <div class="main_top_user_club">
            <?php
            foreach ($results as $key => $value) {?>
                <div class="box_top_user_club flex_club justify_between_club align_center_club">
                    <div class="flex_club align_center_club">
                        <figure>
                            <img src="<?php echo get_avatar_url( $value->ID , 20 ); ?>">
                        </figure>
                        <figcaption>
                            <h6><?php echo get_userdata($value->ID)->first_name.'  '.get_userdata($value->ID)->last_name;?></h6>
                            <span><?php echo $value->count_club;?></span>
                        </figcaption>
                    </div>
                    <span class="user_rank_club"><?php echo $key+1;?></span>
                </div>
            <?php
            }
            ?>
            </div>
        <?php            
        return ob_get_clean();
    }
}


if ( ! function_exists( 'list_gifts' ) ){

    add_shortcode('list_gifts', 'list_gifts');
    
    function list_gifts(){    
        ob_start();
            global $wpdb;
            $results = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}gift_club` ORDER BY `id` DESC;" );
            ?>
            <div class="row_club">
                <?php
                foreach ($results as $key => $value) {?>
                <div class="gifts_box_club">
                    <div class="main_box_club">
                        <div class="content_box_club">
                            <h3><?php echo $value->title; ?></h3>
                            <?php if($value->image){ ?><img src="<?php echo $value->image; ?>" alt="<?php echo $value->title; ?>">
                            <?php }else{ ?>
                                <img src="<?php echo plugins_url('/assets/coupons.png', __FILE__);?>"></img>
                            <?php } ?>
                            <p><?php echo $value->text; ?></p>
                        </div>
                        <div class="footer_box_club flex_club">
                            <div class="btn_box_club">
                                <?php if (count_code_club() >= $value->score) {?>
                                <button type="button" class="btn_club" id="<?php echo $value->id; ?>">دریافت هدیه</button>
                                <?php } else {
                                    $over_score =  (int)$value->score - (int)count_code_club();
                                    ?>
                                    <span><?php echo $over_score;?> امتیاز بیشتر نیاز دارید.</span>
                                    <progress class="progress_box_club" value="<?php echo count_code_club(); ?>" max="<?php echo $value->score; ?>"></progress>
                                <?php } ?>
                            </div>
                            <div class="score_box_club">
                                <span class="score"><?php echo $value->score;?></span>
                                <span>امتیاز</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php
        return ob_get_clean();
    }
}

add_action("wp_ajax_set_gift" , "set_gift");
add_action("wp_ajax_nopriv_set_gift" , "set_gift");
if ( ! function_exists( 'set_gift' ) ){
    function set_gift(){

        if( is_user_logged_in() ){ 

            global $wpdb, $current_user;

            $id = $_POST['id'];
            $userid = $current_user->ID;
            $score_gift = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}gift_club` WHERE `id` = $id ;" );
            $score_user = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}users` WHERE `id` = $userid ;" );
            (int)$count_score_gift = $score_gift[0]->score;
            (int)$count_score_user = $score_user[0]->count_club;
            if($count_score_user < $count_score_gift){
                echo json_encode(array('text' => 'به حد نصاب امتیاز نرسیده اید', 'error' => true));
                die;
            }
            else{
                $new_score = $count_score_user - $count_score_gift;
                $wpdb->get_results("UPDATE `".$wpdb->prefix."users` SET `count_club` = '".$new_score."' WHERE `ID` = '".$userid."';");
                $type = $score_gift[0]->type;
                $dec_custom = $score_gift[0]->dec_custom;
                if($type == '0'){
                    
                    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#!';
                    $type_coupon = $score_gift[0]->type_coupon;
                    $value_type_coupon = $score_gift[0]->value_type_coupon;
                    $min_cart = $score_gift[0]->min_cart;
                    $max_cart = $score_gift[0]->max_cart;
                    $individual_use = $score_gift[0]->individual_use;
                    $exclude_sale_items = $score_gift[0]->exclude_sale_items;
                    $free_shipping = $score_gift[0]->free_shipping;
                    $product_ids = $score_gift[0]->product_ids;
                    $exclude_product_ids = $score_gift[0]->exclude_product_ids;
                    $product_categories = $score_gift[0]->product_categories;
                    $product_categories = explode(',', $product_categories);
                    foreach ($product_categories as $key => $value) {
                        $categories_id .= "i:".$key.";i:".$value.";";
                    }
                    $product_categories = "a:".count($product_categories).":{".$categories_id."}";
                    $exclude_product_categories = $score_gift[0]->exclude_product_categories;
                    $exclude_product_categories = explode(',', $exclude_product_categories);
                    foreach ($exclude_product_categories as $key => $value) {
                        $exclude_categories_id .= "i:".$key.";i:".$value.";";
                    }
                    $exclude_product_categories = "a:".count($exclude_product_categories).":{".$exclude_categories_id."}";

                    $coupon_code = substr(str_shuffle($chars), 0, 10);
                    $text = $score_gift[0]->text; 

                    $coupon = array(
                    'post_title' => $coupon_code,
                    'post_content' => $text,
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_type' => 'shop_coupon');
                    $new_coupon_id = wp_insert_post( $coupon );
                    if ($type_coupon){
                        update_post_meta( $new_coupon_id, 'discount_type', $type_coupon );//نوع کوپن
                    }
                    if ($value_type_coupon){
                        update_post_meta( $new_coupon_id, 'coupon_amount', $value_type_coupon );//مقدار تخفیف
                    }
                    if ($min_cart){
                        update_post_meta( $new_coupon_id, 'minimum_amount', $min_cart );//حداقل مجموع سبد خرید  
                    }
                    if ($max_cart){
                        update_post_meta( $new_coupon_id, 'maximum_amount', $max_cart );//حداکثر مجموع سبد خرید  
                    }
                    if ($individual_use){
                        update_post_meta( $new_coupon_id, 'individual_use', $individual_use );//استفاده تکی
                    }
                    if ($exclude_sale_items){
                        update_post_meta( $new_coupon_id, 'exclude_sale_items', $exclude_sale_items );//عدم استفاده در فروش ویژه
                    }
                    if ($free_shipping){
                        update_post_meta( $new_coupon_id, 'free_shipping', $free_shipping );//حمل و نقل رایگان
                    }
                    if ($product_ids){
                        update_post_meta( $new_coupon_id, 'product_ids', $product_ids );//محصول
                    }
                    if ($exclude_product_ids){
                        update_post_meta( $new_coupon_id, 'exclude_product_ids', $exclude_product_ids );//بجز محصول
                    }
                    if ($product_categories){
                        update_post_meta( $new_coupon_id, 'product_categories', $product_categories );//دسته بندی
                    }
                    if ($exclude_product_categories){
                        update_post_meta( $new_coupon_id, 'exclude_product_categories', $exclude_product_categories );//بجز دسته بندی 
                    }
                    update_post_meta( $new_coupon_id, 'usage_limit', '1' );//تعداد استفاده از کوپن
                    update_post_meta( $new_coupon_id, 'usage_limit_per_user', '1' );//تعداد استفاده از هر کاربر
                    
                    date_default_timezone_set('Asia/Tehran'); 
                    $date = time();
                    $wpdb->get_results( "INSERT INTO `" . $wpdb->prefix . "received_club` (`userid`,  `coupon`, `score`, `type_form`, `dec_received`, `date_received`, `type_received`) VALUES ('".$userid."', '".$coupon_code."', '".$count_score_gift."', '1','".$dec_custom."', '".$date."', '".$type."');" );
                    $count_received = $wpdb->get_results( "SELECT `count_received` FROM `" . $wpdb->prefix . "gift_club` WHERE `ID` = '".$id."';" );
                    $count_received = $count_received[0]->count_received+1;
                    $wpdb->get_results( "UPDATE `" . $wpdb->prefix . "gift_club` SET `count_received` = '".$count_received."' WHERE `ID` = '".$id."';" );
                    echo json_encode(array('popup' => '<div class="coupon_popup_club">'.$coupon_code.'</div>'.$dec_custom));
                    die;
                }
                else{
                    date_default_timezone_set('Asia/Tehran'); 
                    $date = time();
                    $wpdb->get_results( "INSERT INTO `" . $wpdb->prefix . "received_club` (`userid`, `score`, `dec_received`, `type_form`, `date_received`, `type_received`) VALUES ('".$userid."', '".$count_score_gift."', '".$dec_custom."', '1', '".$date."', '".$type."');" );
                    $count_received = $wpdb->get_results( "SELECT `count_received` FROM `" . $wpdb->prefix . "gift_club` WHERE `ID` = '".$id."';" );
                    $count_received = $count_received[0]->count_received+1;
                    $wpdb->get_results( "UPDATE `" . $wpdb->prefix . "gift_club` SET `count_received` = '".$count_received."' WHERE `ID` = '".$id."';" );
                    echo json_encode(array('popup' => $dec_custom));
                    die;
                }
            }
        }
    }
}
if ( ! function_exists( 'add_message_box_club' ) ){
    function add_message_box_club(){?>
        <div class="message_box_club"></div>
        <div class="popup_box_club"></div>
        <?php
        return;
    }
    add_action( 'wp_footer', 'add_message_box_club' );
}
?>