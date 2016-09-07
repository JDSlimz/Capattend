
<?php 

/*
* Plugin Name: CAPAttend
* Description: Civil Air Patrol Attendance Manager
* Version:     0.1
* Author:      Josh Green
* Author URI:  http://joshshouse.us
*/



function plugin_install() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS wp_cap_roles(
        user_id int NOT NULL PRIMARY KEY,
        cap_role text NOT NULL
        ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    $sql = "CREATE TABLE IF NOT EXISTS wp_cap_attendance(
        user_id int NOT NULL,
        date int NOT NULL,
        attendance text NOT NULL,
        reason text,
        PRIMARY KEY (user_id,date)
        ) $charset_collate;";
    
    dbDelta( $sql );
 
}
register_activation_hook( __FILE__, 'plugin_install' );

function member_page() {
    global $wpdb;
    wp_enqueue_style( 'cap-css', plugin_dir_url( __FILE__ ) . '/cap.css',false,'1','all');
    wp_enqueue_style( 'tablesorter-css', plugin_dir_url( __FILE__ ) . '/tablesorter/themes/blue/style.css',false,'1','all');
    wp_enqueue_script( 'cap-js', plugin_dir_url( __FILE__ ) . '/cap.js',false,'1','all');
    wp_enqueue_script( 'tablesorter-js', plugin_dir_url( __FILE__ ) . '/tablesorter/jquery.tablesorter.js',false,'1','all');
    ?><script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script> <?php
    $user_id = get_current_user_id();
    $cap_user = $wpdb->get_results("SELECT * FROM wp_cap_roles WHERE user_id=$user_id");
    $cap_role = $cap_user[0]->cap_role;
    
    //If user is admin and not in the cap rol db, add them.
    if(current_user_can( "administrator" ) && $cap_role == null ){
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "INSERT INTO wp_cap_roles (user_id, cap_role) VALUES ($user_id, 'Senior Staff');";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    } else if($cap_role == "Senior Staff"){
        //Display Senior Staff Page
        ?>

            <div id="senior-parent" class="cap-parent">
                <span id="capMobileMenu" class="dashicons dashicons-menu" onClick="menuToggle()"></span>
                <div id="senior-menu" class="cap-menu">
                    <button id="cadet-checkin" class="btn btn-default" onClick="seniorCheckIn()">Check-In</button>
                    <button id="list-members" class="btn btn-default" onClick="listMembers()">List Members</button><br>
                    <button id="member-attendance" class="btn btn-default" onClick="listSeniorAttendance()">Senior Check-In Report</button><br>
                    <button id="cadet-attendance" class="btn btn-default" onClick="listCadetAttendance()">Cadet Check-In Report</button><br>
                    <button id="add-member" class="btn btn-default" onClick="addMember()">Add Member</button>
                </div>
                <div id="senior-content" class="cap-content">
                    <h2>Welcome to CapAttend.</h2><br>
                    <h4>Please select an option on the left!</h4>
                </div>
            </div>

        <?php
    } else if($cap_role == "Senior Member"){
        //Display Senior Member Page
        ?>
            <div id="senior-parent" class="cap-parent">
                <span id="capMobileMenu" class="dashicons dashicons-menu" onClick="menuToggle()"></span>
                <div id="senior-menu" class="cap-menu">
                    <button id="senior-checkin" class="btn btn-default" onClick="seniorCheckIn()">Check-In</button>
                </div>
                <div id="senior-content" class="cap-content">
                    <h2>Welcome to CapAttend. </h2><br>
                    <h4>Please select an option on the left!</h4>
                </div>
            </div>

        <?php
    } else if($cap_role == "Cadet Staff"){
        //Display Cadet Staff Page
        ?>
            <div id="cadet-parent" class="cap-parent">
                <span id="capMobileMenu" class="dashicons dashicons-menu" onClick="menuToggle()"></span>
                <div id="cadet-menu" class="cap-menu">
                    <button id="cadet-checkin" class="btn btn-default" onClick="cadetCheckIn()">Check-In</button>
                    <button id="cadet-muster" class="btn btn-default" onClick="cadetMuster()">See Cadet Check-Ins</button>
                </div>
                <div id="cadet-content" class="cap-content">
                    <h2>Welcome to CapAttend.</h2><br>
                    <h4>to CapAttend. Please select an option on the left!</h4>
                </div>
            </div>

        <?php
    } else if($cap_role == "Cadet"){
        //Display Cadet Page
        ?>
            <div id="cadet-parent" class="cap-parent">
                <span id="capMobileMenu" class="dashicons dashicons-menu" onClick="menuToggle()"></span>
                <div id="cadet-menu" class="cap-menu">
                    <button id="cadet-checkin" class="btn btn-default" onClick="cadetCheckIn()">Check-In</button>
                </div>
                <div id="cadet-content" class="cap-content">
                    <h2>Welcome to CapAttend.</h2><br>
                    <h4>Please select an option on the left!</h4>
                </div>
            </div>

        <?php
    }
}
add_shortcode("member_page", member_page);