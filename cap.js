function menuToggle(){
    jQuery('.cap-menu').toggle("slide", {direction:'left'});
    jQuery('.cap-menu button').click(function(){
        jQuery('.cap-menu').hide("slide", {direction:'left'});
    });
}

function listMembers(){
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'listMembers'},
        success: function (data) {
            jQuery('#senior-content').html(data);
        },
       error: function () {
           alert("Error");
        },
       dataType : "text"
   });
}

function addMember(){
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'memberForm'},
        success: function (data) {
            jQuery('#senior-content').html(data);
        },
       error: function () {
           alert("Error");
        },
       dataType : "text"
   });
}

function submitMemberForm(){
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'submitMemberForm', fName: jQuery('#memberFirstName').val(), lName: jQuery('#memberLastName').val(), email: jQuery('#memberEmail').val(), role: jQuery('#memberRole').val()},
        success: function (data) {
            listMembers();
        },
       error: function (data) {
           
        },
       dataType : "text"
   });
}

function editMember(member_id, fName, lName, role, email){
    jQuery('.memberTools').html("");
    jQuery('#name-' + member_id).html("<input id='firstName-" + member_id + "' class='form-control'><br><input id='lastName-" + member_id + "' class='form-control'>");
    jQuery('#firstName-' + member_id).val(fName);
    jQuery('#lastName-' + member_id).val(lName);
    
    jQuery('#role-' + member_id).html("\
        <select id='roleSelect-" + member_id + "' class='form-control'>\
            <option value='Cadet'>Cadet</option>\
            <option value='Cadet Staff'>Cadet Staff</option>\
            <option value='Senior Member'>Senior Member</option>\
            <option value='Senior Staff'>Senior Staff</option>\
        </select>\
    ");
    jQuery('#roleSelect-' + member_id).val(role);
    
    jQuery('#email-' + member_id).html("<input id='memberEmail-" + member_id + "' class='form-control'>");
    jQuery('#memberEmail-' + member_id).val(email);
    
    jQuery('#tools-' + member_id).html("<button id='save-" + member_id + "' class='btn btn-success' onClick='saveMember(" + member_id + ")'>Save</button>");
}

function saveMember(member_id){
    var firstName = jQuery('#firstName-' + member_id).val();
    var lastName = jQuery('#lastName-' + member_id).val();
    var role = jQuery('#roleSelect-' + member_id).val();
    var email = jQuery('#memberEmail-' + member_id).val();
    
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'updateMember', id: member_id, fName: firstName, lName: lastName, email: email, role: role},
        success: function (data) {
            listMembers();
        },
       error: function (data) {
           
        },
       dataType : "text"
   });
}

function deleteMember(member_id){
    jQuery('.memberTools').html("");
    jQuery('#tools-' + member_id).html("Are you sure? <br><button id='delet-" + member_id + "' onClick='reallyDelete(" + member_id + ")'>Yes!</button> <button onclick='listMembers()'>No</button>");
}

function reallyDelete(member_id){
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'deleteMember', id: member_id},
        success: function (data) {
            listMembers();
        },
       error: function (data) {
           
        },
       dataType : "text"
   });
}

function cadetCheckIn(){
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'checkinForm'},
        success: function (data) {
            jQuery('#cadet-content').html(data);
        },
       error: function (data) {
           jQuery('#cadet-content').html(data);
        },
       dataType : "text"
   });
}

function seniorCheckIn(){
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'checkinForm'},
        success: function (data) {
            jQuery('#senior-content').html(data);
        },
       error: function (data) {
           jQuery('#senior-content').html(data);
        },
       dataType : "text"
   });
}

function submitCheckIn(userID, date){
    var attendance = jQuery('#attendance').val();
    var reason = jQuery('#reason').val();
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'checkIn', id: userID, date: date, attendance: attendance, reason: reason},
        success: function (data) {
            location.reload();
        },
       error: function (data) {
           jQuery('#senior-content').html(data);
        },
       dataType : "text"
   });
}

function listCadetAttendance(){
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'listCadetAttendance'},
        success: function (data) {
            jQuery('.cap-content').html(data);
        },
       error: function (data) {
           jQuery('#senior-content').html(data);
        },
       dataType : "text"
   });
}

function listSeniorAttendance() {
    jQuery.ajax({
        url: "/wp-content/plugins/capattend/ajax.php",
        type: "POST",
        data: {action: 'listSeniorAttendance'},
        success: function (data) {
            jQuery('#senior-content').html(data);
        },
       error: function (data) {
           jQuery('#senior-content').html(data);
        },
       dataType : "text"
   });
}