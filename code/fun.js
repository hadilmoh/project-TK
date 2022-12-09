function getRequest(request_id) {
    $.ajax({
        type: "GET",
        url: "api.php?request_id=" + request_id,
        success: function (res) {
            if(res.status == 404) {
                alert(res.message);
            }else if(res.status == 200){
                $('#request_id').val(res.data.id);
                $('#u_request_name').val(res.data.name);
                $('#u_day').val(res.data.day);
                $('#u_hour').val(res.data.hour);
                $('#u_users').val(res.data.users);
                var select_data = (res.data.priority);
                $('.select-data option').each(function() {
                    if($(this).val() == select_data) {
                        $(this).prop("selected", true);
                    }
                });
                var dep_users = res.data.dep_users
                var users = res.data.users
                var dep_ids = res.data.dep_ids
                $("#u_departments > option").each(function() {
                    if(dep_ids.includes($(this).val())){
                        $(this).prop("selected", true);
                    }
                });
                
                $("#u_users").empty();
                for (var i = 0; i < dep_users.length; i++) {
                    var id = dep_users[i]['id'];
                    var name = dep_users[i]['name'];                    
                    if(users.includes(id)) {
                        $("#u_users").append("<option selected value='"+id+"'>"+name+"</option>");
                    }else{
                        $("#u_users").append("<option value='"+id+"'>"+name+"</option>");                        
                    }
                    //console.log(users[i]);
                }

                

                // for (var i = 0; i < users.length; i++) {
                //     var id = users[i]['id'];
                //     var name = users[i]['name'];
                //     $("#u_users").append("<option selected value='"+id+"'>"+name+"</option>");
                // }
                // $(res.data.users).each(function() {
                //     if($(this).val() == select_data) {
                //         $(this).prop("selected", true);
                //     }
                // });
                $('#requestEditModal').modal('show');
            }

        }
    });
}
function loadData(depIds) {
    $.ajax({
        url: 'http://ted.test/code/api.php?get_users=1',
        type: 'GET',
        data: {dep_ids:depIds},
        dataType: 'json',error:function(err){
            console.log(err);
        },success:function(response){            
            var users = response;//.data.subjects;
            console.log(users);
            $("#s_users").empty();
            for (var i = 0; i < users.length; i++) {
                var id = users[i]['id'];
                var name = users[i]['name'];
                $("#s_users").append("<option value='"+id+"'>"+name+"</option>");
                //console.log(users[i]);
            }
            $("#u_users").empty();
            for (var i = 0; i < users.length; i++) {
                var id = users[i]['id'];
                var name = users[i]['name'];
                $("#u_users").append("<option value='"+id+"'>"+name+"</option>");
                //console.log(users[i]);
            }
            // $("#subject").empty();
            // for (var i = 0; i < subjects.length; i++) {
            //     var id = subjects[i]['id'];
            //     var name = subjects[i]['name'];
            //     $("#subject").append("<option value='"+id+"'>"+name+"</option>");
            // }
        }
    });
}
function saveRequest(name, day, hour, priority, users){
    console.log(users);
    $.ajax({
        type: "POST",
        url: 'http://ted.test/code/api.php',                    
        data: {
            name : name,
            day : day,
            hour : hour,
            priority : priority,
            users_ids : users,
            save_request : true
        },
        dataType: 'json',error:function(err){
            console.log(err);
        },success:function(response){            
            var users = response;//.data.subjects;
            console.log(response);
        }
    });
};
function updateRequest(name, day, hour, priority, users,request_id){
    console.log(users);
    $.ajax({
        type: "POST",
        url: 'http://ted.test/code/api.php',                    
        data: {
            name : name,
            day : day,
            hour : hour,
            priority : priority,
            users_ids : users,
            request_id : request_id,
            update_request : true
        },
        dataType: 'json',error:function(err){
            console.log(err);
        },success:function(response){            
            var users = response;//.data.subjects;
            console.log(response);
        }
    });

};