function getPlanetResources(ajax_endpoint) {
    $.ajax({
        type: 'get',
        url: ajax_endpoint,
        dataType: 'json',
        success: function (result, textStatus, jqXHR) {
            if (result && result.hasOwnProperty('data')) {
                var resources = result.data.hasOwnProperty('resources') ? result.data.resources : [];
                if (resources.length) {
                    for (var i = 0; i < resources.length; i++) {

                        var resource_id = resources[i].resource_id;
                        var amount = resources[i].amount;
                        $('#planet_resource_' + resource_id).html(amount);
                    }
                }

                var messages = result.data.hasOwnProperty('messages') ? result.data.messages : [];

                if (messages.length) {
                    $('#inner-game-message').html(messages.join('<br>'));
                    $('#game-message').css('display', 'block');
                } else {
                    $('#game-message').css('display', 'none');
                    $('#inner-game-message').html(messages.join('<br>'));
                }
            }
        },
        complete: function () {
            window.setTimeout(function () {
                getPlanetResources(ajax_endpoint)
            }, 1000);
        }
    });
}
