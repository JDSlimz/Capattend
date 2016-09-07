<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );

$POST = preg_replace( "/[^a-zA-Z0-9\s\p{P}]/", '', $_POST );
global $wpdb;

// List Members 
if($POST['action'] == "listMembers"){
    $members = $wpdb->get_results("SELECT * FROM wp_cap_roles");
    ?> <div id="memberList"> <?php
    foreach($members as $member){
        $member_id = $member->user_id;
        $role = $member->cap_role;
        $member_info = $wpdb->get_results("SELECT * FROM wp_users WHERE ID=$member_id");
        $email = $member_info[0]->user_email;
        $firstName = get_user_meta($member_id, "FirstName", true);
        $lastName = get_user_meta($member_id, "LastName", true);
        ?>
            <div class="memberListItem">
                <div id="name-<?php echo $member_id; ?>" class="memberName"><h3><?php echo $firstName . " " . $lastName; ?></h3></div>
                <div id="role-<?php echo $member_id; ?>" class="memberRole"><p><?php echo $role; ?></p></div>
                <div id="email-<?php echo $member_id; ?>" class="memberEmail"><p><?php echo $email;?></p></div>
                <div id="tools-<?php echo $member_id; ?>" class="memberTools">
                    <span class="dashicons dashicons-edit" onClick="editMember(<?php echo $member_id; ?>, '<?php echo $firstName; ?>', '<?php echo $lastName; ?>', '<?php echo $role; ?>', '<?php echo $email ?>')"></span>
                    <span class="dashicons dashicons-trash" onClick="deleteMember(<?php echo $member_id; ?>)"></span>
                </div>
            </div>

        <?php
    }
    ?></div><?php
}
// End List Members

// Add New Member Form
if($POST['action'] == 'memberForm'){
    ?>
    <div class="reformed-form">
        <form method="post" name="AddMember" id="AddMember">
            <dl>
                <dt>
                    <label for="memberFirstName">First Name</label>
                </dt>
                <dd><input type="text" id="memberFirstName" class="required" name="memberFirstName" /></dd>
            </dl>
            <dl>
                <dt>
                    <label for="memberLastName">Last Name</label>
                </dt>
                <dd><input type="text" id="memberLastName" class="required" name="memberLastName" /></dd>
            </dl>
            <dl>
                <dt>
                    <label for="memberEmail">E-Mail</label>
                </dt>
                <dd><input type="text" id="memberEmail" class="required  email" name="memberEmail" /></dd>
            </dl>
            <dl>
                <dt>
                    <label for="memberRole">Role</label>
                </dt>
                <dd>
                    <select size="1" name="memberRole" id="memberRole" class="required">
                        <option value="Cadet">Cadet</option>
                        <option value="Cadet Staff">Cadet Staff</option>
                        <option value="Senior Member">Senior Member</option>
                        <option value="Senior Staff">Senior Staff</option>
                    </select>
                </dd>
            </dl>
            <div id="submit_buttons">
                <button type="submit" onClick="submitMemberForm()">Submit</button>
            </div>
        </form>
    </div>
    <?php
}
// End Add New Member Form

// Submit New Member Form
if($POST['action'] == 'submitMemberForm'){
    $firstName = $POST['fName'];
    $lastName = $POST['lName'];
    $email = $POST['email'];
    $role = $POST['role'];
    $username = substr($firstName, 0, 1) . $lastName;
    $i=1;
    while(username_exists($username)){
        $username = substr($firstName, 0, $i++) . $lastName;
    }
    
    if(email_exists($email)){
        echo "A user with that email already exists!";
    } else {
        $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
        $user_id = wp_create_user( $username, $random_password, $email );
        add_user_meta( $user_id, "FirstName", $firstName);
        add_user_meta( $user_id, "LastName", $lastName);
        //wp_new_user_notification($user_id, null, 'both');
        $wpdb->query("INSERT INTO wp_cap_roles (user_id, cap_role) VALUES ($user_id, '$role')");
        echo "INSERT INTO wp_cap_roles (user_id, cap_role) VALUES ($user_id, '$role')";
    }
}
// End Submit New Member Form

// Update Member Info
if($POST['action'] == 'updateMember'){
    $id = $POST['id'];
    $firstName = $POST['fName'];
    $lastName = $POST['lName'];
    $email = $POST['email'];
    $role = $POST['role'];
    
    update_user_meta($id, "FirstName", $firstName);
    update_user_meta($id, "LastName", $lastName);
    wp_update_user( array ('ID' => $id, 'user_email' => $email) );
    $wpdb->query("UPDATE wp_cap_roles SET cap_role='$role' WHERE user_id=$id");
    
}
// End Update Member Info

// Delete Member
if($POST['action'] == 'deleteMember'){
    $id = $POST['id'];
    require_once(ABSPATH.'wp-admin/includes/user.php' );
    wp_delete_user($id);
    $wpdb->query("DELETE FROM wp_cap_roles WHERE user_id=$id");
}
// End Delete Member

// Weekly Check-In Form
if($POST['action'] == 'checkinForm'){
    global $wpdb;
    date_default_timezone_set('America/New_York');
    $current_date = strtotime("today");
    $today_dow = date('D');
    $user_id = get_current_user_id();
    $firstName = get_user_meta($user_id, "FirstName", true);
    $lastName = get_user_meta($user_id, "LastName", true);
    if($today_dow == 'Mon'){
        $meeting_date = date('d F Y');
        $meeting_timestamp = strtotime("today");
    } else {
        $meeting_date = date('d F Y', strtotime('this monday', strtotime('tomorrow')));
        $meeting_timestamp = strtotime('this monday', strtotime('tomorrow'));
    }
    
    $last_checkin = $wpdb->get_results("SELECT date FROM wp_cap_attendance WHERE user_id=$user_id ORDER BY date DESC LIMIT 1");
    $last_checkin_date = $last_checkin[0]->date;
    
    if($last_checkin_date == $meeting_timestamp) {
        ?> <h2><?php echo $firstName . " " . $lastName; ?> has already checked in for this meeting.</h2> <?php
    } else {
        
        ?>
            <form class="form-horizontal">
                <fieldset>

                <!-- Form Name -->
                <legend>Check-In</legend>

                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="name">Your Name</label>  
                  <div class="col-md-4">
                  <input id="name" name="name" type="text" placeholder="<?php echo $firstName . " " . $lastName; ?>" class="form-control input-md">
                  <span class="help-block">Cannot be changed</span>  
                  </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="date">Meeting Date</label>  
                  <div class="col-md-4">
                  <input id="date" name="date" type="text" placeholder="<?php echo $meeting_date; ?>" class="form-control input-md">
                  <span class="help-block">Cannot be changed</span>  
                  </div>
                </div>

                <!-- Select Basic -->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="attendance">Attending?</label>
                  <div class="col-md-4">
                    <select id="attendance" name="attendance" class="form-control">
                      <option value="Yes">Yes</option>
                      <option value="Late">Late</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                </div>

                <!-- Textarea -->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="reason">Reason (Late/No):</label>
                  <div class="col-md-4">                     
                    <textarea class="form-control" id="reason" name="reason" disabled="disabled" rows="4"></textarea>
                  </div>
                </div>

                <!-- Button -->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="submit"></label>
                  <div class="col-md-4">
                    <button type="button" id="submit" name="submit" class="btn btn-success" onClick="submitCheckIn('<?php echo $user_id; ?>', '<?php echo $meeting_timestamp; ?>')">Check-In</button>
                  </div>
                </div>

                </fieldset>
            </form>

            <script>
                jQuery( "#attendance" ).change(function() {
                    var attending = jQuery('#attendance').val();
                    if(attending == "Late" || attending == "No"){
                        jQuery('#reason').removeAttr("disabled");
                    } else {
                        jQuery('#reason').attr("disabled", "disabled");
                    }
                });
            </script>

        <?php
    }
}
// End Weekly Check-In Form

// Check-In
if($POST['action'] == 'checkIn'){
    global $wpdb;
    $user_id = $POST['id'];
    $date = $POST['date'];
    $attendance = $POST['attendance'];
    $reason = $POST['reason'];
    
    $wpdb->query("INSERT INTO wp_cap_attendance (user_id, date, attendance, reason) VALUES ($user_id, $date, '$attendance', '$reason')");
}
// End Check-In

// List Cadet Attendance
if($POST['action'] == "listCadetAttendance"){
    global $wpdb;
    
    $cadets = $wpdb->get_results("SELECT * FROM wp_cap_roles WHERE cap_role='Cadet' OR cap_role='Cadet Staff'");
    
    $today_dow = date('D');
    if($today_dow == 'Mon'){
        $meeting_date = date('d F Y');
        $meeting_timestamp = strtotime("today");
    } else {
        $meeting_date = date('d F Y', strtotime('this monday', strtotime('tomorrow')));
        $meeting_timestamp = strtotime('this monday', strtotime('tomorrow'));
    }
    
    ?>
    <table id="senior-attend" class="tablesorter">
        <caption>Cadet Check-Ins for <?php echo $meeting_date; ?></caption>
        <thead>
            <tr>
                <th class="table2">Last Name</th>
                <th class="table2">First Name</th>
                <th class="table1">Attending?</th>
                <th class="table4">Reason</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach($cadets as $cadet){
                $firstName = get_user_meta($cadet->user_id, "FirstName", true);
                $lastName = get_user_meta($cadet->user_id, "LastName", true);
                $attendance = $wpdb->get_results("SELECT * FROM wp_cap_attendance WHERE user_id=$cadet->user_id ORDER BY date DESC LIMIT 1");
                
                $lastCheckInDate = $attendance[0]->date;
                $lastCheckInStatus = $attendance[0]->attendance;
                $lastCheckInReason = $attendance[0]->reason;
                ?>
                    <tr>
                        <td><?php echo $lastName; ?></td>
                        <td><?php echo $firstName; ?></td>
                        <?php if(date('d F Y', $meeting_timestamp) == date('d F Y', $lastCheckInDate)){ ?>
                            <td><?php echo $lastCheckInStatus; ?></td>
                            <td colspan="4"><?php echo $lastCheckInReason; ?></td>
                        <?php } else { ?>
                            <td>Not Checked In</td>
                            <td colspan="4"></td>
                        <?php } ?>
                    </tr>
                <?php
            }
            ?>
    
                
        </tbody>
    </table>

    <script>jQuery("#senior-attend").tablesorter({sortList: [[0,0]]});</script>
<?php
}
// End List Cadet Attendance

// List Senior Attendance
if($POST['action'] == "listSeniorAttendance"){
    global $wpdb;
    $today_dow = date('D');
    if($today_dow == 'Mon'){
        $meeting_date = date('d F Y');
        $meeting_timestamp = strtotime("today");
    } else {
        $meeting_date = date('d F Y', strtotime('this monday', strtotime('tomorrow')));
        $meeting_timestamp = strtotime('this monday', strtotime('tomorrow'));
    }
    ?>
    <table id="senior-attend" class="tablesorter">
        <caption>Senior Check-Ins for <?php echo $meeting_date; ?></caption>
        <thead>
            <tr>
                <th class="table2">Last Name</th>
                <th class="table2">First Name</th>
                <th class="table1">Attending?</th>
                <th class="table4">Reason</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $seniors = $wpdb->get_results("SELECT * FROM wp_cap_roles WHERE cap_role='Senior Member' OR cap_role='Senior Staff'");
            foreach($seniors as $senior){
                $firstName = get_user_meta($senior->user_id, "FirstName", true);
                $lastName = get_user_meta($senior->user_id, "LastName", true);
                $attendance = $wpdb->get_results("SELECT * FROM wp_cap_attendance WHERE user_id=$senior->user_id ORDER BY date DESC LIMIT 1");
                
                $lastCheckInDate = $attendance[0]->date;
                $lastCheckInStatus = $attendance[0]->attendance;
                $lastCheckInReason = $attendance[0]->reason;
                ?>
                    <tr>
                        <td><?php echo $lastName; ?></td>
                        <td><?php echo $firstName; ?></td>
                        <?php if(date('d F Y', $meeting_timestamp) == date('d F Y', $lastCheckInDate)){ ?>
                            <td><?php echo $lastCheckInStatus; ?></td>
                            <td colspan="4"><?php echo $lastCheckInReason; ?></td>
                        <?php } else { ?>
                            <td>Not Checked In</td>
                            <td colspan="4"></td>
                        <?php } ?>
                    </tr>
                <?php
            }
        ?>
    
                
        </tbody>
    </table>

    <script>jQuery("#senior-attend").tablesorter({sortList: [[0,0]]});</script>
<?php
}
// End List Senior Attendance