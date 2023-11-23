<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function club_settings_menu(){

    //ایجاد منو اصلی
    add_menu_page( __( 'Customer Club', 'club' ), __( 'Customer Club', 'club' ), 'manage_options', 'club', 'general_function_content', 'dashicons-tickets-alt', 6 ); 

    //ایجاد زیر منو
    add_submenu_page( 'club'/* نام منو اصلی */ , __( 'General', 'club' ) , __( 'General', 'club' ), 'manage_options', 'club' /* iD منو */ , 'general_function_content'/* فانکشن */ );
    add_submenu_page( 'club', __( 'Gifts', 'club' ) , __( 'Gifts', 'club' ), 'manage_options', 'gifts_setting', 'gifts_function_content' );
    add_submenu_page( 'club', __( 'List Gifts', 'club' ) , __( 'GifList Giftsts', 'club' ), 'manage_options', 'list_gifts_setting', 'list_gifts_function_content' );

    add_action( 'admin_init', 'club_custom_setting' );
}
add_action( 'admin_menu', 'club_settings_menu' );

function club_custom_setting(){

    //رجیستر ها
    register_setting( 'club-general-group', 'filde_friend_score' );
    register_setting( 'club-general-group', 'filde_user_score' );

    //بخش ها
    add_settings_section( 'score_section_club', __( 'مقدار امتیاز', 'club') , 'score_section_function', 'club' );
    add_settings_section( 'gifts_section_club', __( 'جوایز', 'club') , 'gifts_section_function', 'gifts_setting' );

    //فیلد ها
    add_settings_field( 'friend_score',  __( 'امتیاز ثبت کننده کد معرف', 'club'), 'filde_friend_score_function', 'club', 'score_section_club', array( 'class' => 'fild_clib' ) );
    add_settings_field( 'user_score',  __( 'امتیاز صاحب کد معرف', 'club'), 'filde_user_score_function', 'club', 'score_section_club', array( 'class' => 'fild_clib' ) );
}

// فانکشن بخش ها
function score_section_function(){
    echo __( 'شخصی سازی مقدار امتیاز دریافتی هر شخص', 'club');
}
function gifts_section_function(){
    echo __( 'شخصی سازی جوایز دریافتی', 'club');
}
//فانکشن فیلد ها
function filde_friend_score_function(){
    $option =  get_option( 'filde_friend_score' , 10);
    $option = ($option) ? $option : 0;
    echo '<div class="input_number"><input type="number" name="filde_friend_score" value="'.$option.'" placeholder="مثال : 50" min="0" step="1" /></div>';
}
function filde_user_score_function(){
    $option = get_option( 'filde_user_score' , 10);
    $option = ($option) ? $option : 0;
    echo '<div class="input_number"><input type="number" name="filde_user_score" value="'.$option.'" placeholder="مثال : 50" min="0" step="1"  /></div>';
}

if ( ! function_exists( 'header_club' ) ){
    function header_club(){?>
        <div class="header_club">
            <h1>افزونه باشگاه مشتریان وردپرس</h1>    
            <div class="flex justify-between">
                <p>وقتشه بازدید کننده ها رو به مشتری و کاربر تبدیل کنی!</p>
                <ul class="menu_club flex align-center">
                    <li><a href="#">پشتیبانی</a></li>
                    <li><a href="#">تلگرام</a></li>
                    <li><a href="#">واتساپ</a></li>
                    <li class="version_club">V 1.1.1</li>
                </ul>
            </div>
        </div>
    <?php
    return;
    }
}

function general_function_content(){
    header_club();
    global $wpdb; 
    settings_errors();
    $count_received_cuopon = $wpdb->get_results( "SELECT count(*) as total FROM `". $wpdb->prefix ."received_club` WHERE `type_form` = 1 AND `type_received` = 0 ;" );
    $count_received_cuopon =  $count_received_cuopon[0]->total;
    $count_received_custom = $wpdb->get_results( "SELECT count(*) as total FROM `". $wpdb->prefix ."received_club` WHERE `type_form` = 1 AND `type_received` = 1 ;" );
    $count_received_custom =  $count_received_custom[0]->total;
    $list_received = $wpdb->get_results( "SELECT * FROM `". $wpdb->prefix ."gift_club` ORDER BY `count_received` DESC limit 9;" );
    
    ?>
    <div class="row">
        <div class="w25">
            <div class="box_analyze">
                <span><?php echo count_users()['total_users'];?></span>
                <p>تعداد کل کاربران</p>
            </div> 
        </div>
        <div class="w25">
            <div class="box_analyze">
                <span><?php $results = $wpdb->get_results( "SELECT COUNT(*) as total FROM `". $wpdb->prefix ."gift_club`" ); echo $results[0]->total;?></span>
                <p>تعداد کل هدایا</p>
            </div> 
        </div>
        <div class="w25">
            <div class="box_analyze">
                <span><?php $results = $wpdb->get_results( "SELECT COUNT(*) as total FROM `". $wpdb->prefix ."received_club` WHERE `type_form` = 0" ); echo $results[0]->total;?></span>
                <p>تعداد کل کد های ثبت شده</p>
            </div> 
        </div>
        <div class="w25">
            <div class="box_analyze">
                <span><?php echo $count_received_cuopon+$count_received_custom;?></span>
                <p>تعداد هدایای استفاده شده </p>
            </div> 
        </div>
    </div>
    <div class="row">
        <div class="w30">
            <h2>نوع انتخابی هدیه کاربران</h2>
            <div class="type_gifts ct-chart ct-perfect-fourth"></div>
            <p> <span class="color_chart a"></span> هدیه کد تخفیف 
                <br>
                <span class="color_chart b"></span> هدیه سفارشی
            </p>
        </div>
        <div class="w70">
            <h2>تعداد انتخاب هر هدیه توسط کاربران</h2>
            <div class="gifts ct-chart ct-perfect-fourth"></div>
        </div>
    </div>

    
    <?php
        foreach ($list_received as $key => $value) {
            $title .= "'".$value->title."',";
            $count .= $value->count_received.",";
        }
    ?>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
    <script>
        new Chartist.Bar('.gifts', { 
            labels: [<?php echo $title;?>], 
            series: [[<?php echo $count;?>]]
        }, {
            height: 330
        });


        var data = {
            series: [<?php echo $count_received_cuopon.",".$count_received_custom; ?>]
        };

        var sum = function(a, b) { return a + b };

        new Chartist.Pie('.type_gifts', data, {
            labelInterpolationFnc: function(value) {
                return Math.round(value / data.series.reduce(sum) * 100) + '%';
            }
        });
    </script>
    <H2>تنظیمات</H2>
    <div class="flex">
        <div class="w30">
            <form action="options.php" method="post">
                <?php 
                do_settings_sections( 'club' );
                settings_fields( 'club-general-group' );
                submit_button(  __( 'Save', 'club') );
                ?>
            </form>
        </div>
        <div class="w70">
            <div class="help_add_gifts">
                <h2>راهنما</h2>
                <p>برای آموزش کامل افزونه و دسترسی به تمامی شورت کد ها به بخش راهنما افزونه مراجه کنید</p>
                <ul class="ul_club">
                    <li>لطفا متن راهنمای هر فیلد را مطالعه کنید</li>
                    <li>در صورت مواجه با مشکل به پشتیبانی اطلاع دهید</li>
                </ul>
            </div>
        </div>
    </div>
<?php
}//محتوا منو اصلی عمومی
add_action("wp_ajax_gifts_function_content" , "gifts_function_content");
add_action("wp_ajax_nopriv_gifts_function_content" , "gifts_function_content");
function gifts_function_content(){
    header_club();
    global  $wpdb;
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $results = $wpdb->get_results( "SELECT * FROM `". $wpdb->prefix ."gift_club` WHERE `id` = '".$id."';" );
    }
    ?>
    <form action="" method="post" class="form_add_gift row">
        <div class="row w75">
            <div class="w50">
                <h2>افزودن هدیه باشگاه مشتریان</h2>
                <input type="text" name="text_gift" id="title_gift" placeholder="نام هدیه (الزامی)" value="<?php if($results[0]->title){  echo $results[0]->title; }?>">
                <div class="flex justify-between">
                    <label for="score_gift">مقدار امتیاز مورد نیاز (الزامی)</label>
                    <div class="input_number">
                        <input type="number" name="score_gift" id="score_gift" step="1" value="<?php if($results[0]->score){ echo $results[0]->score; }else { echo '0'; }?>" min="0">
                    </div>
                </div>
                <textarea name="dec_gift" id="dec_gift" rows="10" placeholder="توضیح هدیه (الزامی)"><?php if($results[0]->text){  echo $results[0]->text; }?></textarea>
                <label>تصویر هدیه</label>
                <div class="flex">
                    <button class="button button-secondary button_club" id="upload-button"></button>
                    <input type="hidden" name="image_gift" id="image_gift" value="<?php if($results[0]->image){  echo $results[0]->image; }?>">
                    <img class="picture-preview" src="<?php if($results[0]->image){  echo $results[0]->image.'"'.'style="display: block;"'; }?>" >
                </div>
                <p>جهت حذف تصویر روی آن کلیک کنید.</p>
            </div>
            <div class="w50">
                <h2>نوع هدیه</h2>
                <div class="mb10">
                    <label for="coupon_gift" class="input_radio">
                        <input type="radio" name="select_gift" checked class="select_gift" id="coupon_gift" value="0">    
                        کد تخفیف
                    </label>
                    <label for="custom_gift" class="input_radio">
                        <input type="radio" name="select_gift" class="select_gift" id="custom_gift" value="1" <?php if($results[0]->type == 1){ echo 'checked'; }?>>
                        سفارشی
                    </label>
                </div>
                <div class="main_coupon_gift" <?php if($results[0]->type == 1){ echo 'style="display: none;"'; }?>>
                    <div class="flex justify-between">
                        <label for="type_coupon">مقدار تخفیف</label>
                        <select name="type_coupon" id="type_coupon">
                            <option value="percent" <?php if($results[0]->type_coupon == 'percent'){echo 'selected';}?>>درصد تخفیف</option>
                            <option value="fixed_cart" <?php if($results[0]->type_coupon == 'fixed_cart'){echo 'selected';}?>>تخفیف سبد خرید ثابت</option>
                            <option value="fixed_product" <?php if($results[0]->type_coupon == 'fixed_product'){echo 'selected';}?>>تخفیف محصول ثابت</option>
                        </select>
                    </div>
                    <div class="flex justify-between">
                        <label for="score_gift">مقدار امتیاز مورد نیاز</label>
                        <div class="input_number">
                            <input type="number" name="value_type_coupon" id="value_type_coupon" step="1" value="<?php if($results[0]->value_type_coupon){ echo $results[0]->value_type_coupon; }else { echo '0'; }?>" min="0">
                        </div>
                    </div>
                    <label for="free_shipping" class="block mb10">
                        <input type="checkbox" name="free_shipping" id="free_shipping" class="switch" value="yes" <?php if($results[0]->free_shipping == 'yes'){echo 'checked';}?>>
                        حمل و نقل رایگان
                    </label>
                    <div class="flex">
                        <input type="number" min="0" name="min_cart" id="min_cart" class="ml5" placeholder="حداقل مجموع سبد خرید" value="<?php if($results[0]->min_cart){ echo $results[0]->min_cart; }?>">
                        <input type="number" min="0" name="max_cart" id="max_cart" class="mr5" placeholder="حداکثر مجموع سبد خرید" value="<?php if($results[0]->max_cart){ echo $results[0]->max_cart; }?>">
                    </div>
                    <label for="individual_use" class="block mb10">
                        <input type="checkbox" name="individual_use" class="switch" id="individual_use" value="yes" <?php if($results[0]->individual_use == 'yes'){echo 'checked';}?>>
                        استفاده تکی ار کد تخفیف
                    </label>
                    <label for="exclude_sale_items" class="block mb10">
                        <input type="checkbox" name="exclude_sale_items" class="switch" id="exclude_sale_items" value="yes" <?php if($results[0]->exclude_sale_items == 'yes'){echo 'checked';}?>>
                        بجز محصولات دارای تخفیف (فروش ویژه)
                    </label>
                    <div class="flex">
                        <input type="text" class="ml5 mb0" placeholder="محصول (id)" name="product_ids" id="product_ids" value="<?php if($results[0]->product_ids){ echo $results[0]->product_ids; }?>">
                        <input type="text" class="mr5 mb0" name="exclude_product_ids" id="exclude_product_ids" placeholder="بجز محصول (id)" value="<?php if($results[0]->exclude_product_ids){ echo $results[0]->exclude_product_ids; }?>">
                    </div>
                    <p>برای جدا سازی مقایر لطفا از , استفاده نماید مانند : 1,2,3</p>
                    <div class="flex">
                        <input type="text" class="ml5 mb0" name="product_categories" id="product_categories" placeholder="دسته بندی (id)" value="<?php if($results[0]->product_categories){ echo $results[0]->product_categories; }?>">
                        <input type="text" class="mr5 mb0" name="exclude_product_categories" id="exclude_product_categories" placeholder="بجز دسته بندی (id)" value="<?php if($results[0]->exclude_product_categories){ echo $results[0]->exclude_product_categories; }?>">
                    </div>
                    <p>برای جدا سازی مقایر لطفا از , استفاده نماید مانند : 1,2,3</p>
                </div>
                <textarea name="dec_custom_gift" id="dec_custom_gift"  placeholder="یاداشت هدیه بعد از دریافت (الزامی)"><?php if($results[0]->dec_custom){echo $results[0]->dec_custom;}?></textarea>
            </div>
        </div>
        <div class="w25">
            <div class="help_add_gifts">
                <h2>راهنما</h2>
                <p>برای آموزش کامل افزونه و دسترسی به تمامی شورت کد ها به بخش راهنما افزونه مراجه کنید</p>
                <ul class="ul_club">
                    <li>لطفا متن راهنمای هر فیلد را مطالعه کنید</li>
                    <li>پس از ایجاد هدیه مورد نظر در بخش لیست هدایا میتوانید آن را مشاهده ، ویرایش یا حذف کنید</li>
                    <li>در صورت مواجه با مشکل به پشتیبانی اطلاع دهید</li>
                </ul>
            </div>
            <button type="button" <?php if(isset($_GET['id'])){ echo 'id_gifts="'.$_GET['id'].'"'; }?> class="<?php if(isset($_GET['id'])){ echo 'btn_update_gifts';}else{ echo 'btn_send_gifts'; }?> button_club"><?php if(isset($_GET['id'])){ echo 'بروزرسانی هدیه';}else{ echo 'انتشار هدیه'; }?></button>
        </div>
    </form>
    <div class="message_box_club"></div>
<?php
}//محتوا منو افزودن هدایا

function list_gifts_function_content(){
    header_club();?>
    <h2>لیست هدیه های باشگاه مشتریان</h2>
    <div class="main_table">
        <?php
        global $wpdb;
        $per_page = 10;
        $page_num = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $start = $per_page * $page_num;
        $start = $start - $per_page;
        $results = $wpdb->get_results( "SELECT * FROM `". $wpdb->prefix ."gift_club` ORDER BY `id` DESC LIMIT $start , $per_page;" );
        if(!empty($results)){
        ?>
        <table id="table">
            <thead>
                <tr>
                    <td class="td_image_gift">تصویر</td>
                    <td class="td_id_gift">شناسه</td>
                    <td>عنوان</td>
                    <td>نوع</td>
                    <td>امتیاز مصرفی</td>
                    <td class="td_action_gift">ویرایش | حذف</td>
                </tr>
            </thead>

            <tbody>
                <?php 
                foreach ($results as $key => $value) {
                ?>
                <tr id="<?php echo $value->id?>">
                    <td><?php if($value->image):?><img src="<?php echo $value->image?>"><?php endif;?></td>
                    <td class="td_id_gift"><?php echo $value->id?></td>
                    <td><?php echo $value->title?></td>
                    <td><?php
                    if($value->type == '0'){
                        echo "کد تخفیف";
                    }else{
                        echo "سفارشی";
                    }
                    ?></td>
                    <td><?php echo $value->score?></td>
                    <td>
                        <div class="action_list_gift">
                            <a href="<?php echo admin_url( 'admin.php?page=gifts_setting&id='.$value->id ); ?>" class="edit_gift" >ویرایش</a>
                            <a class="delete_gift" id="<?php echo $value->id?>">حذف</a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="message_box_club"></div>
        <?php 
        btn_nav_page($per_page, $page_num,admin_url('admin.php?page=list_gifts_setting&'));
        }
        else {?>
        <p class="empty">هدیه ای وجود ندارد</p>
        <?php } ?>
    </div>
<?php 
}//محتوا منو لیست هدایا


add_action("wp_ajax_add_gift_club" , "add_gift_club");
add_action("wp_ajax_nopriv_add_gift_club" , "add_gift_club");
if ( ! function_exists( 'add_gift_club' ) ){
    function add_gift_club(){
        global $wpdb;

        $title =  $_POST['title'];
        $score =  $_POST['score'];
        $dec =  $_POST['dec'];
        $image =  $_POST['image'];
        $type =  $_POST['type'];
        $dec_custom =  $_POST['dec_custom'];
        if(empty($title) || empty($score) || empty($dec) || empty($dec_custom)){
            echo json_encode(array('text' => 'لطفا موارد خواسته شده ( الزامی ) را پر کنید', 'error' => true));
            die;
        }
        elseif($score < 0){
            echo json_encode(array('text' => 'مقادیر عددی نباید منفی باشد', 'error' => true));
            die;
        }
        elseif($type == '0'){
            $type_coupon =  $_POST['type_coupon'];
            $value_type_coupon =  $_POST['value_type_coupon'];
            $free_shipping = $_POST['free_shipping'];
            $min_cart = $_POST['min_cart'];
            $max_cart = $_POST['max_cart'];
            $individual_use = $_POST['individual_use'];
            $exclude_sale_items = $_POST['exclude_sale_items'];
            $product_ids = $_POST['product_ids'];
            $exclude_product_ids = $_POST['exclude_product_ids'];
            $product_categories = $_POST['product_categories'];
            $exclude_product_categories = $_POST['exclude_product_categories'];
            /**********/
            if($value_type_coupon < 0 || $min_cart < 0 || $max_cart < 0 || $product_ids < 0 || $exclude_product_ids < 0 || $product_categories < 0 || $exclude_product_categories < 0){
                echo json_encode(array('text' => 'مقادیر عددی نباید منفی باشد', 'error' => true));
                die;
            }
            $wpdb->get_results( "INSERT INTO `". $wpdb->prefix ."gift_club` (`id`, `title`, `score`, `text`, `image`, `type`, `dec_custom`, `type_coupon`, `value_type_coupon`, `free_shipping`, `min_cart`, `max_cart`, `individual_use`, `exclude_sale_items`, `product_ids`, `exclude_product_ids`, `product_categories`, `exclude_product_categories`) VALUES (NULL, '".$title."', '".$score."', '".$dec."', '".$image."', '".$type."', '".$dec_custom."', '".$type_coupon."', '".$value_type_coupon."', '".$free_shipping."', '".$min_cart."', '".$max_cart."', '".$individual_use."', '".$exclude_sale_items."', '".$product_ids."', '".$exclude_product_ids."', '".$product_categories."', '".$exclude_product_categories."');" );
            echo json_encode(array('text' => 'با موفقیت ثبت شد', 'error' => false));
            die;
        }
        else{
            $wpdb->get_results( "INSERT INTO `". $wpdb->prefix ."gift_club` (`id`, `title`, `score`, `text`, `image`, `type`, `dec_custom`) VALUES (NULL, '".$title."', '".$score."', '".$dec."', '".$image."', '".$type."', '".$dec_custom."');" );
            echo json_encode(array('text' => 'با موفقیت ثبت شد', 'error' => false));
            die;
        }
    }
}


add_action("wp_ajax_update_gift_club" , "update_gift_club");
add_action("wp_ajax_nopriv_update_gift_club" , "update_gift_club");
if ( ! function_exists( 'update_gift_club' ) ){
    function update_gift_club(){
        global $wpdb;
        
        $id = $_POST['id'];
        $title =  $_POST['title'];
        $score =  $_POST['score'];
        $dec =  $_POST['dec'];
        $image =  $_POST['image'];
        $type =  $_POST['type'];
        $dec_custom =  $_POST['dec_custom'];
        if(empty($title) || empty($score) || empty($dec) || empty($dec_custom)){
            echo json_encode(array('text' => 'لطفا موارد خواسته شده ( الزامی ) را پر کنید', 'error' => true));
            die;
        }
        elseif($score < 0){
            echo json_encode(array('text' => 'مقادیر عددی نباید منفی باشد', 'error' => true));
            die;
        }
        elseif($type == '0'){
            $type_coupon =  $_POST['type_coupon'];
            $value_type_coupon =  $_POST['value_type_coupon'];
            $free_shipping = $_POST['free_shipping'];
            $min_cart = $_POST['min_cart'];
            $max_cart = $_POST['max_cart'];
            $individual_use = $_POST['individual_use'];
            $exclude_sale_items = $_POST['exclude_sale_items'];
            $product_ids = $_POST['product_ids'];
            $exclude_product_ids = $_POST['exclude_product_ids'];
            $product_categories = $_POST['product_categories'];
            $exclude_product_categories = $_POST['exclude_product_categories'];
            /**********/
            $wpdb->get_results( "UPDATE `". $wpdb->prefix ."gift_club` SET `title` = '".$title."', `score` = '".$score."', `text` = '".$dec."', `image` = '".$image."', `type` = '".$type."', `dec_custom` = '".$dec_custom."', `type_coupon` = '".$type_coupon."', `value_type_coupon` = '".$value_type_coupon."', `free_shipping` = '".$free_shipping."', `min_cart` = '".$min_cart."', `max_cart` = '".$max_cart."', `individual_use` = '".$individual_use."', `exclude_sale_items` = '".$exclude_sale_items."', `product_ids` = '".$product_ids."', `exclude_product_ids` = '".$exclude_product_ids."', `product_categories` = '".$product_categories."', `exclude_product_categories` = '".$exclude_product_categories."' WHERE `wp_gift_club`.`id` = $id;" );
            if($value_type_coupon < 0 || $min_cart < 0 || $max_cart < 0 || $product_ids < 0 || $exclude_product_ids < 0 || $product_categories < 0 || $exclude_product_categories < 0){
                echo json_encode(array('text' => 'مقادیر عددی نباید منفی باشد', 'error' => true));
                die;
            }
            echo json_encode(array('text' => 'با موفقیت بروز شد', 'error' => false));
            die;
        }
        else{
            $wpdb->get_results( "UPDATE `". $wpdb->prefix ."gift_club` SET `title` = '".$title."', `score` = '".$score."', `text` = '".$dec."', `image` = '".$image."', `type` = '".$type."', `dec_custom` = '".$dec_custom."', `type_coupon` = '', `value_type_coupon` = '', `free_shipping` = '', `min_cart` = '', `max_cart` = '', `individual_use` = '', `exclude_sale_items` = '', `product_ids` = '', `exclude_product_ids` = '', `product_categories` = '', `exclude_product_categories` = '' WHERE `wp_gift_club`.`id` = $id;" );
            echo json_encode(array('text' => 'با موفقیت بروز شد', 'error' => false));
            die;
        }
    }
}

add_action("wp_ajax_delete_gift_club" , "delete_gift_club");
add_action("wp_ajax_nopriv_delete_gift_club" , "delete_gift_club");
if ( ! function_exists( 'delete_gift_club' ) ){
    function delete_gift_club(){
        global $wpdb;
        $id =  $_POST['id'];
        $wpdb->get_results( "DELETE FROM `". $wpdb->prefix ."gift_club` WHERE `id` = $id" );
        echo json_encode(array('text' => 'با موفقیت حذف شد', 'error' => false));
        die;
    }
}

if ( ! function_exists( 'btn_nav_page' ) ){
    function btn_nav_page($per_page, $page_num, $url){

        global $wpdb;
        
        $results = $wpdb->get_results( "SELECT COUNT(*) as total FROM `". $wpdb->prefix ."gift_club`" );
        $total_page = (ceil($results[0]->total / $per_page));
        $prev = $page_num - 1;
        if ($results[0]->total > $per_page):
        ?>
        <div class="flex justify-center align-center pagination_club">
            <?php if( $page_num <= 1 ) {?>
                <span><</span>
            <?php } else {?>
               <a href="<?php echo $url."page_num=".$prev ?>"><</a>
            <?php } 
            for( $i = 1; $i <= $total_page; $i++ ){
                if($i==$page_num) { ?>
                    <span class='active'><?php echo $i; ?></span>
                <?php }
                else { ?>
                    <a href="<?php echo $url."page_num=".$i; ?>"><?php echo  $i; ?></a>
                <?php }
            }
            $next = $page_num+1;
            if($page_num>=$total_page) {?>
                <span>></span>
            <?php } else { ?>
                <a href="<?php echo $url."page_num=".$next; ?>">></a>
            <?php } ?>
        </div>
       <?php  
       endif;
       return;
    }
}