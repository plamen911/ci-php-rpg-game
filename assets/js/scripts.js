function getPlanetResources(ajax_endpoint) {
    $.ajax({
        type: 'get',
        url: ajax_endpoint,
        dataType: 'json',
        success: function (result, textStatus, jqXHR) {
            if (result && result.hasOwnProperty('data')) {
                for (var i = 0; i < result.data.length; i++) {
                    var resource_id = result.data[i].resource_id;
                    var amount = result.data[i].amount;
                    $('#planet_resource_' + resource_id).html(amount);
                }
            }
        }
    });
}