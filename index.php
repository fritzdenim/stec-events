<?php 
/*
Plugin Name: sTec Events
Plugin URI: http://www.stec-inc.com/
Description: This plugin will contain events
Version: 0.1-beta
Author: sTec Web Team
Author URI: http://www.stec-inc.com
*/

function stec_events() {
    $args = array(
    'public' => true,
    'description' => 'An events manager plugin for sTec',
    'exclude_from_search' => false,
    'has_archive' => true,
    // Sets the fields
    'supports' => array(
      'title',
      'editor',
      //'revisions',
      'page-attributes',
    ),
    // Sets all the labels
    'labels' => array(
      'name' => 'Events',
      'singular_name' => 'Event',
      'add_new' => 'Add New',
      'add_new_item' => 'Add New Event',
      'edit_item' => 'Edit Event',
      'new_item' => 'New Event',
      'view_item' => 'View Event',
      'search_items' => 'Search Event',
      'not_found' => 'No Event Found',
      'not_found_in_trash' => 'No Event found in trash',
      'parent_item_colon' => 'Parent Events:',
      'edit' => 'Edit',
      'view' => 'View',
    ),
    // Should be set to true to allow use of capabilities
    'map_meta_cap' => true,
    // Sets all the capabilities
    'capability_type' => array('stec_event','stec_events'),
    'capabilities' => array(
      'publish_posts' => 'publish_stec_events',
      'edit_published_posts' => 'edit_published_stec_events',
      'delete_published_posts' => 'delete_published_stec_events',
      'edit_posts' => 'edit_stec_events',
      'edit_others_posts' => 'edit_other_stec_events',
      'delete_posts' => 'delete_stec_events',
      'delete_others_posts' => 'delete_others_stec_events',
      'read_private_posts' => 'read_private_stec_events',
      'edit_post' => 'edit_stec_event',
      'delete_post' => 'delete_stec_event',
      'read_post' => 'read_stec_events'
    ),
    'query_var' => 'stec_events',
    'rewrite' => array('slug' => 'event'),
    'menu_position' => 150,
  );
  register_post_type('stec_events',$args);
}

// sTec Event Presenters
function add_stec_event_presenter_screen() {
  global $post;
  $presentations = get_post_meta($post->ID,'stec_event_presentations',TRUE);
  $data = json_decode($presentations,true);
  
  echo '<table class="table" style="width:100%;text-align:left;">';
  echo '<tr><th>Presenter</th><th>Presentation</th><th>Title</th><th>Time</th><th>Location</th><th>Action</th></tr>';
  echo '<tr>';
  echo '<th><input style="width:100%;" type="text" title="Presenter" id="stec_event_presenter" name="stec_event_presenter" placeholder="Presenter" value="" /></th>';
  echo '<th><input style="width:100%;" type="text" title="Presentation" id="stec_event_presentation" name="stec_event_presentation" placeholder="Presentation" value="" /></th>';
  echo '<th><input style="width:100%;" type="text" title="Title" id="stec_event_title" name="stec_event_title" placeholder="Title" value="" /></th>';
  echo '<th><input style="width:100%;" type="text" title="Time" id="stec_event_time" name="stec_event_time" placeholder="Time" value="" /></th>';
  echo '<th><input style="width:100%;" type="text" title="Location" id="stec_event_location" name="stec_event_location" placeholder="Location" value="" /></th>';
  echo '<th style="text-align:left;"><input type="submit" id="stec_event_add_presenter" name="stec_event_add_presenter" value="Add Presenter" class="button" /></th>';
  echo '</tr>';
  // The results of all the stec_event_presenters.
  echo '<input type="hidden" id="presenterId" name="presenterId" value="" />';
  
  // Get the presentation size
  $maxNumber = max(array_keys($data['presentations']));
  // display data
  for ($i=0;$i<=$maxNumber;$i++) {
    if ($data["presentations"][$i]!=null) {
      echo '<tr id="presenterRow'. $i .'">';
      echo '<td>' . $data["presentations"][$i]["presenter"] . '</td>';
      echo '<td>' . $data["presentations"][$i]["presentation"] . '</td>';
      echo '<td>' . $data["presentations"][$i]["title"] . '</td>';
      echo '<td>' . $data["presentations"][$i]["time"] . '</td>';
      echo '<td>' . $data["presentations"][$i]["location"] . '</td>';
      echo '<td><input type="submit" title="Delete Presenter" onclick="deletePresenter(\''.$i.'\')" class="button" id="stec_event_delete_presenter" name="stec_event_delete_presenter" value="Delete"></td>';
      echo '</tr>';
    }
  }
  echo '</tr>';
  echo '</table>';
}

// sTec Event Attributes Screen
function add_stec_event_screen() {
  global $post;
  $custom = get_post_custom($post->ID);
  wp_nonce_field( plugin_basename( __FILE__ ), 'stec_event_attributes' );
  $stec_date_start = $custom['stec_event_start_date'][0];
  $stec_date_end = $custom['stec_event_end_date'][0];
  $stec_address = $custom['stec_address'][0];
  $stec_url = $custom['stec_url'][0];
  $stec_video_url = $custom['stec_video_url'][0];
  $stec_booth_number = $custom['stec_booth_number'][0];
 
  // Displays all the attributes
  echo '<ul>';
  echo '<li>' . stec_event_date('Start','stec_event_start_date',$stec_date_start) . '</li>';
  echo '<li>' . stec_event_date('End','stec_event_end_date',$stec_date_end) . '</li>';
  echo '<li>' . stec_event_text('Address','stec_address',$stec_address) . '</li>';
  echo '<li>' . stec_event_text('URL','stec_url',$stec_url) . '</li>';
  echo '<li>' . stec_event_text('Event URL or Video URL','stec_video_url',$stec_video_url) . '</li>';
  echo '<li>' . stec_event_text('Booth Number','stec_booth_number',$stec_booth_number) . '</li>';
  echo '</ul>';
}

// Text field
function stec_event_text($name,$field,$value) {
  return '<input style="width:100%;" type="text" title="' . esc_attr($name) . '" id="' . esc_attr($field) . '" name="' . esc_attr($field) . '" placeholder="' . esc_attr($name) . '" value="' . $value. '" />';
}

// Date field
function stec_event_date($label,$name,$date) {
  $stec_time = empty($date) ? current_time('timestamp') : $date;
?>
  <div class="timestamp-wrap">
    <label for="<?php echo $name; ?>"><?php echo $label; ?></label>
    <select id="<?php echo $name; ?>_month" name="<?php echo $name; ?>_month">
      <?php 
        $mymonth = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        for ($i=0;$i<12;$i++) {
          $monthnum = zeroise($i+1, 2);
          $month .= "\t\t\t" . '<option value="' . $monthnum . '"';
          if ( $i+1 == (gmdate('m',$stec_time))) $month .= ' selected="selected"';
          /* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
          $month .= '>' . (zeroise($monthnum,2)) . "-" . $mymonth[$i] . "</option>\n";
        }
        // Display the current time.
        echo '<div class="curtime hide-if-js" style="display:block;">';
        echo $month;
        echo '</div>';
      ?>
    </select>
    <input type="text" id="<?php echo $name; ?>_day" name="<?php echo $name; ?>_day" size="2" value="<?php echo gmdate('d',$stec_time); ?>" maxlength="2" autocomplete="off" style="width:25px" />, 
    <input type="text" id="<?php echo $name; ?>_year" name="<?php echo $name; ?>_year" size="4" value="<?php echo gmdate('Y',$stec_time); ?>" maxlength="4" autocomplete="off" style="width:40px" />@ 
    <input type="text" id="<?php echo $name; ?>_hour" name="<?php echo $name; ?>_hour" size="2" value="<?php echo gmdate('H',$stec_time); ?>" maxlength="2" autocomplete="off" style="width:25px" />:
    <input type="text" id="<?php echo $name; ?>_minute" name="<?php echo $name; ?>_minute" size="2" value="<?php echo gmdate('i',$stec_time); ?>" maxlength="2" autocomplete="off" style="width:25px" />
    <!-- <input type="hidden" id="<?php echo $name; ?>" name="<?php echo $name; ?>" value="" /> -->
  </div>
<?php }

// Save the data
function save_stec_event() {
  global $post;
  $se = 'stec_event';
  $sd = $se.'_start_date';
  $ed = $se.'_end_date';
  $start_date = mktime($_POST[$sd.'_hour'],$_POST[$sd.'_minute'],0,$_POST[$sd.'_month'],$_POST[$sd.'_day'],$_POST[$sd.'_year']);
  $end_date = mktime($_POST[$ed.'_hour'],$_POST[$ed.'_minute'],0,$_POST[$ed.'_month'],$_POST[$ed.'_day'],$_POST[$ed.'_year']);

  $data = getJsonArray();
  // Clean up null objects
  for ($i=0;$i<sizeof($data);$i++) {
    if($data['presentations'][$i]==null) {
      unset($data['presentations'][$i]);
    }
  }
  $new_presentation = createNewPresentationArray($_POST);
  
  if ($_POST['presenterId']!="") {
    // If the presenterId is not blank, you can delete using presenterId number
    $id = $_POST['presenterId'];
    unset($data['presentations'][$id]);
  } elseif ($new_presentation!==null) {
    array_push($data['presentations'],$new_presentation);
  }
  
  update_post_meta($post->ID,'stec_event_start_date', $start_date);
  update_post_meta($post->ID,'stec_event_end_date', $end_date);
  update_post_meta($post->ID,'stec_address', $_POST['stec_address']);
  update_post_meta($post->ID,'stec_url', $_POST['stec_url']);
  update_post_meta($post->ID,'stec_video_url', $_POST['stec_video_url']);
  update_post_meta($post->ID,'stec_booth_number', $_POST['stec_booth_number']);
  update_post_meta($post->ID,'stec_event_presentations', json_encode($data));
}

function getJsonArray() {
  global $post;
  // Initialize the presentations
  $db_presentations = get_post_meta($post->ID,'stec_event_presentations',TRUE);
  $data = '';
  if (empty($db_presentations)) {
    $presentations = '{"presentations": [{}]}';
  } else {
    $presentations = get_post_meta($post->ID,'stec_event_presentations',TRUE);
  }
  $data = json_decode($presentations, true);
  return $data; // presentations from post_meta database
}

function createNewPresentationArray($p) {
  // Check if there is data on stec_event_presenter POST attributes. If there is, then prepare the data.
  if(!empty($p['stec_event_presenter']) || !empty($p['stec_event_presentation']) || !empty($p['stec_event_title']) || !empty($p['stec_event_time']) || !empty($p['stec_event_location'])) {
    $presentation = '
      {
        "presenter" :"' . $p['stec_event_presenter'] . '",
        "presentation" : "' . $p['stec_event_presentation'] . '",
        "title" : "' . $p['stec_event_title'] . '",
        "time" : "' . $p['stec_event_time'] . '",
        "location" : "' . $p['stec_event_location'] . '"
      }
    ';
  }
  
  $new_presentation = json_decode($presentation);
  return $new_presentation;
}

function delete_stec_event() {
  global $post;
  delete_post_meta($post->ID,'stec_event_start_date');
  delete_post_meta($post->ID,'stec_event_end_date');
  delete_post_meta($post->ID,'stec_address');
  delete_post_meta($post->ID,'stec_url');
  delete_post_meta($post->ID,'stec_video_url');
  delete_post_meta($post->ID,'stec_booth_number');
}

function add_stec_event_meta_box() {
  add_meta_box('stec_event_presenter_id',__('Event Presenters'),'add_stec_event_presenter_screen','stec_events');
  add_meta_box('stec_event_id',__('Event Attributes'),'add_stec_event_screen','stec_events');
}

function stec_event_add_style_and_script() { ?>
  <style>
    .table {
      border-collapse: collapse;
    }
    .table tr th {
      padding-top: 0px;
      padding-bottom: 10px;
    }
    .table tr td {
      padding: 10px 0;
      border-top: 1px solid #afafaf;
    }
  </style>
  <script type="text/javascript">
    function deletePresenter(id) {
      //alert(id);
      document.getElementById('presenterId').value=id;
    }
  </script>
<?php }

add_action('save_post','save_stec_event');
add_action('publish_post','save_stec_event');
add_action('delete_post','delete_stec_event');

add_action('init','stec_events');
add_action('admin_head','stec_event_add_style_and_script');
add_action('add_meta_boxes','add_stec_event_meta_box');
add_action('add_action','stec_event_text');
add_action('add_action','stec_event_date');


