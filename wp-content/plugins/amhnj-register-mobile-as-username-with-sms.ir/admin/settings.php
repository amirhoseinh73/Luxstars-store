<?php

function register_mobile_global_settings() {
	title();
    // ranges();
}

function title () {
	?>
		<h1>
			تنظیمات
		</h1>
        <hr/>
        <p>برای اجرای فرم شورتکد زیر را در صفحه مورد نظر خود کپی کنید</p>
        <p><strong>[amhnj_register_form]</strong></p>

        <footer>
            <p>نوشته شده توسط 
                <a href="https://instagram.com/amirhoseinh73" target="_blank">amirhosein hasani</a>
            </p>
        </footer>
	<?php
}

/*
function ranges() {
    global $wpdb;
    // $units_sold = $product->get_total_sales();

    $table_name = $wpdb->prefix . 'progress_discount_ahh';

    $result = $wpdb->get_results ( "
        SELECT * 
        FROM  $table_name
            WHERE id = 1
    " );

    if ( exists( $result ) ) $result = $result[0];

    $def_val_1 = 250;
    $def_val_2 = 500;
    $def_val_3 = 1000;
    $sold_items_manually = 0;
    $date = "تا 30 بهمن";
    if ( exists( $result ) ) {
        $def_val_1           = $result->range_1;
        $def_val_2           = $result->range_2;
        $def_val_3           = $result->range_3;
        $sold_items_manually = $result->sold_items_manually;
        $date                = $result->date;
        $range_1_price       = $result->range_1_price;
        $range_2_price       = $result->range_2_price;
        $range_3_price       = $result->range_3_price;
        $product_id          = $result->product_id;
        $original_price      = $result->original_price;
    }
    ?>
	<form method="POST">
        <label>شناسه محصول مورد نظر</label>
		<input type="text" id="product_id" value="<?= $product_id?>" name="product_id"/>

        <hr/>
		<label>بازه 1</label>
		<input type="text" id="first_range" value="<?= $def_val_1?>" name="first_range"/>

        <hr/>
        <label>بازه 2</label>
		<input type="text" id="second_range" value="<?= $def_val_2?>" name="second_range"/>

        <hr/>
        <label>بازه 3</label>
		<input type="text" id="third_range" value="<?= $def_val_3?>" name="third_range"/>

        <hr/>
        <label>قیمت بازه 1</label>
		<input type="text" id="range_price_1" value="<?= $range_1_price?>" name="range_price_1"/>

        <hr/>
        <label>قیمت بازه 2</label>
		<input type="text" id="range_price_2" value="<?= $range_2_price?>" name="range_price_2"/>

        <hr/>
        <label>قیمت بازه 3</label>
		<input type="text" id="range_price_3" value="<?= $range_3_price?>" name="range_price_3"/>

        <hr/>
        <label>تعداد کل فروش دستی</label>
		<input type="text" id="sold_items_manually" value="<?= $sold_items_manually?>" name="sold_items_manually"/>
        <!-- <span>تعداد محصولات فروخته شده آنلاین:
            <?//= $units_sold?>
        </span> -->
        <!-- <span>
            تعداد کل فروخته شده
            <?//= intval( $units_sold ) + intval( $sold_items_manually )?>
        </span> -->

        <hr/>
        <label>تاریخ</label>
		<input type="text" id="date" value="<?= $date?>" name="date"/>

        <!-- <hr/>
		<label>قیمت اصلی محصول موزد نظر</label>
		<input type="text" id="original_price" value="<?= $original_price?>" name="original_price"/> -->

        <hr/>
		<button type="submit" name="save">submit</button>
	</form>
	<?php
	if ( isset($_POST["save"]) ) {
		$val_1                    = $def_val_1;
        $val_2                    = $def_val_2;
        $val_3                    = $def_val_3;
        $sold_items_manually_post = $sold_items_manually;
        $date_post                = $date;
        $range_price_1_post       = $range_1_price;
        $range_price_2_post       = $range_2_price;
        $range_price_3_post       = $range_3_price;
        $product_id_post          = $product_id;
        $original_price_post      = $original_price;
        if ( exists( $_POST["first_range"] ) && exists( $_POST["second_range"] ) && exists( $_POST["third_range"] ) ) {
            $val_1                    = $_POST['first_range'];
            $val_2                    = $_POST['second_range'];
            $val_3                    = $_POST['third_range'];
            $sold_items_manually_post = $_POST['sold_items_manually'];
            $date_post                = $_POST['date'];
            $range_price_1_post       = $_POST['range_price_1'];
            $range_price_2_post       = $_POST['range_price_2'];
            $range_price_3_post       = $_POST['range_price_3'];
            $original_price_post      = $_POST['original_price'];
			$product_id_post		  = $_POST['product_id'];
            try {

                $wpdb->update( $table_name, array( 
                    "range_1" 				=> $val_1,
                    "range_2" 				=> $val_2,
                    "range_3" 				=> $val_3,
                    "sold_items_manually" 	=> $sold_items_manually_post,
                    "date" 				    => $date_post,
                    "range_1_price"		    => $range_price_1_post,
                    "range_2_price"		    => $range_price_2_post,
                    "range_3_price"		    => $range_price_3_post,
                    "product_id"		    => $product_id_post,
                    "original_price"        => $original_price_post,
                ), 
                    array(
                        'id' => 1
                    )
                );

                // $wpdb->insert(
                //     $table_name,
                //     array(
                        // "range_1" 				=> $val_1,
                        // "range_2" 				=> $val_2,
                        // "range_3" 				=> $val_3,
                        
                //     )
                // );

                echo "<div class='notice notice-success'>
                <p>
                عملیات با موفقیت انجام شد
                </p>
                </div>";
            } catch (\Exception $e) {
                echo "<div class='notice notice-error'>
                <p>
                عملیات انجام نشد
                </p>
                </div>";
            }

            
        } else {
            echo "<div class='notice notice-error'>
                <p>
                عملیات انجام نشد
                </p>
                </div>";
        }

        echo "<script type='text/javascript'>
                // setTimeout( () => {
                    document.getElementById('first_range').value = {$val_1};
                    document.getElementById('second_range').value = {$val_2};
                    document.getElementById('third_range').value = {$val_3};
                    document.getElementById('product_id').value = {$product_id_post};
                    document.getElementById('range_price_1').value = {$range_price_1_post};
                    document.getElementById('range_price_2').value = {$range_price_2_post};
                    document.getElementById('range_price_3').value = {$range_price_3_post};
                    document.getElementById('sold_items_manually').value = {$sold_items_manually_post};
                    document.getElementById('date').value = ' {$date_post} ';
                    document.getElementById('original_price').value = ' {$original_price_post} ';
                    // document.location.reload();
                // },1000 );
            </script>";
	}
}

**/