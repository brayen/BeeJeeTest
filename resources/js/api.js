$(document).ready(function() {

    // key generate
    $('#generate').on('click', function() {
        $.ajax({
            url: '/user/apiKey/generate',
            type: 'post',
            datatype: 'json',
            success: (res) => {
                $('#api-key').html(res.api_key);
            },
        })
    })

    // copy to clipboard
    $('#copy').click(function() {
        const textArea = document.createElement("textarea");
        textArea.value = $('#api-key').text();

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            document.execCommand('copy');
            $('.clipboard-msg').fadeIn(1000, function () {
                $(this).delay(1000).fadeOut(1000);
            });
        } catch (err) {
            console.error('Unable to copy to clipboard', err);
        }
        document.body.removeChild(textArea);
    });
})
