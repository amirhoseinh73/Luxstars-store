<?php

// require_once AMHNJ_REGISTER_PLUGIN_ADMIN_PATH . "public.php";
require_once AMHNJ_REGISTER_PLUGIN_ADMIN_PATH . "database.php";
require_once AMHNJ_REGISTER_PLUGIN_ADMIN_PATH . "menu.php";
require_once AMHNJ_REGISTER_PLUGIN_ADMIN_PATH . "settings.php";



function table_html() {
	?>
		<div class="vira-admin-table-parent">
			<table class="vira-admin-table">
				<thead>
					<th>#</th>
					<th>نام</th>
					<th>نام خانوادگی</th>
					<th>کد ملی</th>
					<th>شماره تماس</th>
					<th>نوع کاربری</th>
					<th>جنسیت</th>
					<th>اهراز هویت شماره تماس</th>
					<th>پایه</th>
					<th>رشته</th>
					<th>تاریخ ایجاد</th>
					<th>آخرین به روز رسانی</th>
				</thead>
				<tbody>
					<?php $data = load_table_demo(); ?>
				</tbody>
			</table>
		</div>
	<?php

	paginate($data['num_of_pages'], $data['pageNumber']);
}

function load_table_demo(){
	global $wpdb;
	$table = $wpdb->prefix . 'vira_user_demo_info';

	$pageNumber = (isset($_GET['page_number']) ? $_GET['page_number'] : 1);

	$pageNumber = (isset($pageNumber) && !empty($pageNumber)) ? absint($pageNumber) : 1;
	$limit = 25;
	$offset = ($pageNumber - 1) * $limit;
	$total = $wpdb->get_var("SELECT COUNT(`id`) FROM {$table}");
	$num_of_pages = ceil( $total / $limit );

	$get_all_user = $wpdb->get_results("SELECT * FROM {$table} LIMIT {$limit} OFFSET {$offset}");
	if (isset($get_all_user) && !empty($get_all_user)) {
		$i = 0;
		$teacher	 = 'معلم';
		$student	 = 'دانش آموز';
		$male		 = 'آقا';
		$female	 	 = 'خانم';
		$confirm 	 = 'تایید شده';
		$not_confirm = 'تایید نشده';
		while ($i < count($get_all_user)) {
			$user = $get_all_user[$i];
			echo '<tr>
					<td>' . ($i + 1) . '</td>
					<td>' . $user->first_name . '</td>
					<td>' . $user->last_name . '</td>
					<td>' . $user->nat_code . '</td>
					<td>' . $user->phone . '</td>
					<td>' . (($user->type_user == 1) ? $teacher : $student) . '</td>
					<td>' . (($user->gender == 1) ? $male : $female) . '</td>
					<td class="'. (($user->mobile_verified) ? 'text-green' : 'text-red' ) . '">' . (($user->mobile_verified) ? $confirm : $not_confirm) . '</td>
					<td>' . $user->grade . '</td>
					<td>' . $user->field . '</td>
					<td>' . $user->created_on . '</td>
					<td>' . $user->updated_on . '</td>
				</tr>';
			$i++;
		}
	} else {
		echo '<tr class="text-red"><td colspan="13">کاربری وجود ندارد.</td></tr>';
	}

	return array(
		'pageNumber'   => $pageNumber,
		'num_of_pages' => $num_of_pages,
	);
}

function paginate($num_of_pages, $pageNumber) {
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'page_number', '%#%' ),
		'format' => '',
		'prev_text' => __( '&laquo;', 'text-domain' ),
		'next_text' => __( '&raquo;', 'text-domain' ),
		'total' => $num_of_pages,
		'current' => $pageNumber
	) );
	
	if ( $page_links ) {
		echo '<div class="vira-admin-paginate">
				<div>' . $page_links . '</div>
			</div>';
	}
}