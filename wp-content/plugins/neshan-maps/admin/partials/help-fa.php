<?php

/**
 * Provide admin my maps view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://platform.neshan.org
 * @since      1.1.1
 *
 * @package    Neshan_Maps
 * @subpackage Neshan_Maps/admin/partials
 */

?>

<div class="wrap neshan_help_rtl" id="neshan_help_wrapper">
    <h1 class="wp-heading-inline">
		<?php _e( 'Neshan Maps Help', 'neshan-maps' ); ?>
        <small> نگارش<?php echo $this->version; ?></small>
    </h1>

    <hr class="wp-header-end">

    <div class="neshan_help">
        بسیاری از وب‌سایت‌هایی که از سیستم مدیریت محتوای Wordpress استفاده می‌کنند در بخش‌هایی از وب‌سایت خود از جمله صفحه‌ی تماس با ما، از نقشه‌ برای جانمایی موقعیت جغرافیایی خود استفاده می‌کنند. همچنین سایت‌های مبتنی بر وردپرسی که تمام یا بخشی از حوزه‌ی فعالیت‌شان در زمینه انتشار اطلاعات مکان‌محور مانند نمایش مکان رستوران‌ها یا نمایندگی‌های یک شرکت و مانند این‌ها بر روی نقشه می‌باشد نیز از سرویس نقشه بهره می‌برند. سایت‌های تخصصی دیگری نیز بر پایه‌ی وردپرس وجود دارند که نیازمند نمایش نقشه در سایت خود هستند.
        <hr>
        برخی لینک‌های مفید که به شما در استفاده‌ی بهتر از نقشه‌ی نشان کمک می‌کنند:
        <ul>
            <li>
                <a href="https://developer.neshan.org" target="_blank">
                    ثبت‌نام و دریافت API Key رایگان از پنل توسعه‌دهندگان نشان
                </a>
            </li>
            <li>
                <a href="https://neshan.blog/wordpress-neshan-maps-plugin-msdvteihyzkw" target="_blank">
                    افزونه وردپرس جایگزین نقشه گوگل
                </a>
            </li>
            <li>
                <a href="https://neshan.blog/wordpress-plugin-neshan-dynamic-maps-llwbta5egdoq" target="_blank">
                    ایجاد نقشه‌ی داینامیک در وردپرس
                </a>
            </li>
        </ul>
    </div>

    <div class="neshan_help">
        <h1>
            نقشه‌ی استاندارد چیست و چگونه آن را تولید کنم؟
        </h1>
        نقشه‌ی استاندارد در واقع یک نقشه‌ی ساده است که شما می‌توانید به آسانی از طریق
        <a href="<?php echo admin_url( 'admin.php?page=neshan_maps_create' ); ?>">
            ابزار تولید نقشه
        </a>
        آن را تولید کرده و سپس از شورتکدی که در اختیار شما قرار می‌گیرد در هر صفحه‌ی وردپرس استفاده کنید.
        توسط این ابزار به راحتی می‌توانید ابعاد، سطح بزرگنمایی و استایل نقشه را مدیریت کنید.
        همچنین امکان ویرایش تنظیمات نقشه حتی بعد از تولید آن نیز برای شما فراهم است. کافیست به صفحه‌ی
        <a style="text-decoration: none;" href="<?php echo admin_url( 'admin.php?page=neshan_maps' ); ?>">
            نقشه‌های من
        </a>
        مراجعه و نقشه‌ی موردنظر خود را انتخاب و ویرایش نمایید.
        <br>
        همچنین در بخش نقشه‌های من امکان حذف یک نقشه نیز وجود دارد. اما دقت کنید چنانچه یک نقشه را پاک کنید امکان بازگرداندن آن وجود ندارد.
        به علاوه در صورت حذف یک نقشه شورتکد آن نیز از کار خواهد افتاد.
    </div>

    <div class="neshan_help">
        <h1>
            چگونه نقشه‌ی داینامیک ایجاد کنم؟
        </h1>
        نقشه‌های داینامیک نقشه‌هایی هستند که بدون نیاز با ابزار تولید نقشه قابل اجرا هستند.
        به این معنی که شما در هر قسمت سایت وردپرسی خود (برگه‌ها، نوشته‌ها، کدهای قالب و ...) می‌توانید با استفاده از شورتکد نقشه‌ی داینامیک، نقشه‌ی مورد نظر خود را ایجاد کنید.
        <br>
        برای ایجاد نقشه‌ی داینامیک کافی است از شورتکد زیر استفاده کنید. پارامترهای مختلف این شورتکد نیز در ادامه معرفی می‌گردند:
        <pre>
            [neshan-map-dynamic]
        </pre>
        یک نمونه‌ی کامل استفاده از این شورتکد بصورت زیر می‌باشد:
        <pre>
            [neshan-map-dynamic api_key="<span class="red">YOUR_API_KEY</span>" lat="<span>35.700301</span>″ lng="<span>51.351953</span>″ width="<span>100%</span>" height="<span>400px</span>" maptype="<span>neshan</span>"]
        </pre>

        پارامترهای این شورتکد عبارتند از:
        <ul>
            <li>
                <strong>api_key</strong>:
                کلید دسترسی Web SDK که از پنل توسعه‌دهندگان نشان دریافت کرده‌اید. چنانچه هنوز این کلید را دریافت نکرده‌اید می‌توانید با مراجعه به
                <a href="https://developer.neshan.org/" target="_blank">
                    این لینک
                </a>
                به صورت رایگان ثبت‌نام و کلید دسترسی برای Web SDK را دریافت کنید.
            </li>
            <li>
                <strong>lat</strong>:
                عرض جغرافیایی مکان مورد نظر به عنوان مثال 35.700301
            </li>
            <li>
                <strong>lng</strong>:
                طول جغرافیایی مکان مورد نظر به عنوان مثال 51.351953
            </li>
            <li>
                <strong>width</strong>:
                عرض نقشه که به دو فرمت قابل استفاده است. هم می‌توانید بصورت درصد مقدار آن را ثبت کنید و هم به صورت پیکسل.
            </li>
            <li>
                <strong>height</strong>:
                ارتفاع نقشه که به دو فرمت درصد و پیکسل قابل استفاده است. اما توصیه می‌شود برای ارتفاع همیشه از واحد پیکسل استفاده کنید. به عنوان مثال 450px
            </li>
            <li>
                <strong>maptype</strong>:
                از طریق این پارامتر می‌توانید استایل نقشه را مشخص کنید. در حال حاضر نقشه‌های نشان با چهار استایل زیبا و متنوع قابل استفاده هستند.
                این استایل‌ها عبارتند از:
                <ul>
                    <li>
                        neshan
                    </li>
                    <li>
                        standard-day
                    </li>
                    <li>
                        standard-night
                    </li>
                    <li>
                        osm-bright
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="neshan_help">
        <h1>
            نیاز به کمک دارید؟
        </h1>
        چنانچه در استفاه از این افزونه و یا سرویس‌های نقشه‌ی نشان سوال، ابهام و یا پینشهادی در راستای بهبود آن دارید می‌توانید از طریق پست الکترونیک زیر با ما در تماس باشید:
        <a href="mailto:developer@neshan.org" target="_blank">
            developer@neshan.org
        </a>
    </div>
</div>