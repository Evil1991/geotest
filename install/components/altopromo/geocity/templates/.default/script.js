$(
    function () {

        let geotext = document.getElementById('geocity_text_value');

        $('#geocity_yes').click(
            function () {
                $('#geocity_popup').hide();
            }
        );

        $('#geocity_no').click(
            function () {
                $('#geocity_popup').hide();
                $('#geocity_popup_window').show();
            }
        );

        $('#realcity_agree').click()
        {
            $('#geocity_popup_window').hide();

            let cityID = $('#realcity').val();
            let cityName = $('#realcity_' + cityID).html();

            $.ajax({
                url: '/local/altopromo/geocity/ajax.php',
                method: 'post',
                data: {'action': 'selectCity', 'id': cityID},
                success: function () {
                    geotext.innerHTML = cityName;
                    geotext.dataset.id = cityID;
                }
            });
        }
    }
)