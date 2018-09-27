(function($, window)
{
    function get(url)
    {
        alert(0);
    }

	function onhashchange() 
	{
        if (__vandaServerVars.spa == false)
            return;

        // if (window.location.indexOf('upload') > -1)
        //     return;

        var url;
		var hash = location.hash;

        if (hash)
        {
            url = $('a[href="' + hash + '"]').attr('data-url');
            var arr;

            if (url == '' || typeof url === 'undefined')
            {
                // split by # to prevent replace ':' in 'http://'
                url = window.location.href;
                arr = url.split('#');
                arr[1] = arr[1].replace(':', '?id=');
                url = arr.join('/');
            }

            var delimeter;
            var className;

            if (hash.indexOf(':') > -1)
                delimeter = ':';
            else if (hash.indexOf('?') > -1)
                delimeter = '?';

            if (delimeter)
            {
                arr = hash.split(delimeter);
                className = arr[0];
            }
            else
                className = hash;

            className = className.replace('#', '');
            className = className.replace(/\//g, '-');

            $('li.' + className).addClass('active');
            $('li.' + className + ' ul').removeClass('collapse');
        }
        else
            url = __vandaServerVars.homeUrl;

        $.ajax({
            url: url,
            data: {'_': Math.random()},
            dataType: 'json',
            cache: false,
            success: function(response)
            {
                alert(9);
                if (response.redirect)
                {
                    if (response.redirect.indexOf('login') > -1)
                        window.location.href = __vandaServerVars.homeUrl;
                    else
                        window.location.href = response.redirect;
                }
                else if (/<html>/i.test($.trim(response.content)))
                    window.location.href = __vandaServerVars.homeUrl;
                else
                {
                    document.title = response.title;
                    $('.content').html(response.content);

                    getDocumentReady();
                }
            },
            error: function()
            {
                alert('Failed to get: ' + url);
            }
        });
	}
	
	$(window).on('hashchange', onhashchange);
	$(onhashchange);

})(jQuery, this);
