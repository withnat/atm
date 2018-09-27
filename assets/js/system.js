$(document).ready(function(){
	getDocumentReady();
});

function getDocumentReady(ajaxLevel)
{
	if (ajaxLevel === undefined)
		ajaxLevel = 1;

	$('.i-checks').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'iradio_square-green',
	});

	//var elem = document.querySelector('.js-switch');
	//Switchery(elem, {color: '#1AB394'});

	var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

	elems.forEach(function(elem){
		//var switchery = new Switchery(elem, {color: '#1AB394'});
		Switchery(elem, {color: '#1AB394'});
	});

	$('.summernote').summernote();

	if (ajaxLevel == 1)
	{
		$('.search-form input[type=text]').keyup(function(){
			delay(function(){
                __vandaSaveSearchForm();
                __vandaGoToPage(1);
				__vandaAutoLoadDataTable();
			}, 200);
		});

		$('.search-form select').change(function(){
			delay(function(){
                __vandaSaveSearchForm();
                __vandaGoToPage(1);
				__vandaAutoLoadDataTable();
			}, 200);
		});

		$('.search-form button.submit').on('click', function(event) {
			event.preventDefault();
            __vandaSaveSearchForm();
            __vandaGoToPage(1);
			__vandaAutoLoadDataTable();
		});

		$('.search-form button.reset').on('click', function(event){
			event.preventDefault();
            __vandaClearSearchForm();
			//$(this).closest('form').get(0).reset();

			$(':input', '.search-form')
				.removeAttr('checked')
				.removeAttr('selected')
				.not(':button, :submit, :reset, :hidden, :radio, :checkbox')
				.val('');

            __vandaGoToPage(1);
			__vandaAutoLoadDataTable();
		});

		/*
		$('input.datepicker').each(function(index){
			if ($(this).val() && $(this).val().indexOf('-') > -1)
			{
				if ($(this).val().indexOf('0000-00-00') > -1)
					$(this).val('');
				else {
					var arr = $(this).val().split('-');
					var date = arr[2] + '/' + arr[1] + '/' + arr[0];

					$(this).val(date);
				}
			}
		});
		*/

		$('.input-group.date').datepicker({
			format: 'yyyy-mm-dd',
			todayBtn: 'linked',
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true
		});

		if (__vandaServerVars.spa)
		{
			$('form').on('submit', function(event){
				event.preventDefault();
				$.ajax({
					type: 'post',
					url: $(this).attr('action'),
					data: $(this).serialize(),
					dataType: 'json',
					cache: false,
					success: function(response){
						if (response.content)
							$('.flash').html(response.content);
						else if (response.redirect)
							window.location.href = response.redirect;
					}
				});
			});
		}

        __vandaRestoreSearchForm();
	}

	$('#checkall-toggle').on('ifToggled', function(){
		__vandaPaginatorToggleCheckAll();
		__vandaPaginatorToggleToolbarButton();
	});

	$('input[name=id\\[\\]]').on('ifToggled', function(){
		__vandaPaginatorToggleToolbarButton();
	});
}

function __vandaSaveSearchForm()
{
    var formString = vanda.formToString($('.search-form'));
    var context = __vandaContext();

    Cookies.set(context + 'searchform', formString, {expires: 365, path: '/'});
}

function __vandaRestoreSearchForm()
{
    var context = __vandaContext();
    var formString = Cookies.get(context + 'searchform');

    if (isJSONString(formString))
        vanda.stringToForm(formString, $('.search-form'));
}

function __vandaClearSearchForm()
{
    var context = __vandaContext();
    Cookies.remove(context + 'searchform', { path: '/' });
}

function isJSONString(str)
{
	try {
		$.parseJSON(str);
	} catch (e) {
		return false;
	}

	return true;
}

function __vandaContext()
{
	var url
	var context;
	var arr;

	if ($('.datatable').length)
		url = $('.datatable table').attr('data-url');
	else
		url = window.location.href;

	arr = url.split('?');
	url = arr[0];
	context = url.replace(/[^a-z0-9]/gi, '');

	return context;
}

function __vandaAutoLoadDataTable(url)
{
    $('.datatable').LoadingOverlay('show', {
        image: '',
        fontawesome: 'fa fa-spinner fa-spin',
        zIndex: '9999'
    });

    if (url === undefined)
		url = $('.datatable table').attr('data-url');

	if (url === undefined)
		return;

	var data = $('.search-form').serializeArray();

	$.ajax({
        type: 'post',
		url: url,
		data: data,
		cache: false,
		success: function(response)
		{
			if (isJSONString(response))
			{
				response = $.parseJSON(response);

				if (response.redirect)
				{
					if (response.redirect.indexOf('login') > -1)
						window.location.href = __vandaServerVars.homeUrl;
					else
						window.location.href = response.redirect;
				}
				else if (response.content)
					$('.flash').html(response.content);
			}
			else if (/<html>/i.test($.trim(response)))
				window.location.href = __vandaServerVars.homeUrl;
			else
			{
			   $('.datatable').html(response);

				getDocumentReady(2);
			}

            $('.datatable').LoadingOverlay('hide', true);
		},
		error: function()
		{
            bootbox.alert('Failed to get: ' + url);
            $('.datatable').LoadingOverlay('hide', true);
		}
	});
}

function __vandaPaginatorDataTables()
{
	$('.i-checks').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'iradio_square-green',
	});

	$('#checkall-toggle').iCheck('uncheck');

	$('#checkall-toggle').on('ifToggled', function(){
		__vandaPaginatorToggleCheckAll();
		__vandaPaginatorToggleToolbarButton();
	});

	$('input[name=id\\[\\]]').on('ifToggled', function(){
		__vandaPaginatorToggleToolbarButton();
	});

	__vandaPaginatorToggleToolbarButton();
}

function __vandaPaginatorToggleCheckAll()
{
	if ($('#checkall-toggle').prop('checked'))
		$('input[name=id\\[\\]]').iCheck('check');
	else
		$('input[name=id\\[\\]]').iCheck('uncheck');
}

function __vandaPaginatorToggleToolbarButton()
{
	if ($('input[name=id\\[\\]]:checked').length > 0)
		$('.btn-toolbar-toggle').removeClass('hide');
	else
		$('.btn-toolbar-toggle').addClass('hide');
}

function __vandaGetIds()
{
	var id = [];

	$('input[name=id\\[\\]]').each(function (){
		if (this.checked)
			id.push($(this).val());
	});

	return id;
}

function __vandaDoAction(url)
{
	$.ajax({
		url: url,
		data: {id: __vandaGetIds()},
		cache: false,
		success: function(response)
		{
			if (isJSONString(response))
			{
				response = $.parseJSON(response);

				if (response.redirect)
				{
					if (response.redirect.indexOf('login') > -1)
						window.location.href = __vandaServerVars.homeUrl;
					else
						window.location.href = response.redirect;
				}
				else if (response.content)
					$('.flash').html(response.content);
			}
			else if (/<html>/i.test($.trim(response)))
				window.location.href = __vandaServerVars.homeUrl;
			else
			{
				$('.flash').html(response);
				__vandaAutoLoadDataTable();
			}
		}
	});
}

function __vandaSortPage(sortcol)
{
	var context = __vandaContext();
	var sortdir = Cookies.get(context + 'sortdir');

	sortdir = (sortdir == 'ASC') ? 'DESC' : 'ASC';

	Cookies.set(context + 'sortcol', sortcol, {expires: 365, path: '/'});
	Cookies.set(context + 'sortdir', sortdir, {expires: 365, path: '/'});

	__vandaAutoLoadDataTable();
}

function __vandaSetPageSize(pagesize)
{
	var context = __vandaContext();

	Cookies.set(context + 'pagesize', pagesize, {expires: 365, path: '/'});

	__vandaAutoLoadDataTable();
}

function __vandaGoToPage(page)
{
	var context = __vandaContext();

	Cookies.set(context + 'page', page, {path: '/'});

	__vandaAutoLoadDataTable();
}

function __vandaReplaceUrlParam(url, paramName, paramValue)
{
	if (paramValue === undefined)
		paramValue = '';

	var pattern = new RegExp('\\b('+paramName+'=).*?(&|$)');

	if (url.search(pattern) >= 0)
		return url.replace(pattern, '$1' + paramValue + '$2');

	return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
}

var delay = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();

// Since there is (currently) no built-in method for this, you'd need to add your own method. This would work:
$.validator.addMethod('length', function(value, element, param) {
	return this.optional(element) || value.length == param;
}, $.validator.format('Please enter exactly {0} characters.'));

/* still not use
function ucfirst(str)
{
	str = str.charAt(0).toUpperCase() + str.slice(1);
	return str;
}
*/
