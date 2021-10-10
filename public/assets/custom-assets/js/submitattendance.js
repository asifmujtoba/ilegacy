async function submitAttendance(){
    let check = $('.check');
    let text ='';
    let check_attendance = await $.ajax({url: '/api/users/getattendance'});
    if(!check_attendance && check.text() == 'Check In' || check.text() == 'Check Out'){
        await $.ajax({url: '/api/users/registerattendance'});
        if(check.text() == "Check Out"){
            text = 'Checked Out'; 
        }
        else if(check.text() == 'Check In'){
            text = 'Check Out';
            
        }
        check.text(text); 
    }
    else{
        return;
    }
}