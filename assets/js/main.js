console.log(100500);

jQuery('.exist-roles').on('change', function(e){
    console.log(jQuery(this).val());
    var loc = window.location;
    loc = loc + '&name=' + jQuery(this).val();
    location.replace(loc);
});

jQuery('.exist-users').on('change', function(e){
    console.log(jQuery(this).val());
    var loc = window.location;
    loc = loc + '&user_id=' + jQuery(this).val();
    location.replace(loc);
});

