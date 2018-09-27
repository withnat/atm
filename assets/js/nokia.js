function getNewHeight(ori_width, ori_height)
{
    var width = 300;
    var height = 300;

    if (ori_width > width || ori_height > height)
    {
        new_height = (ori_height * width) / ori_width;

        if (new_height > height)
            new_height = height;
        else
            new_height = (ori_height * width) / ori_width;
    }
    else
        new_height = ori_height;

    return new_height;
}

function setImage(name, obj)
{
    var imgPath = obj.value;
    var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
    var previewId = '#' + name + 'Preview';
    var preview = $(previewId);
    var image = $(previewId + ' img');

    if (extn == 'gif' || extn == 'png' || extn == 'jpg' || extn == 'jpeg')
    {
        if (typeof(FileReader) != 'undefined')
        {
            image.remove();
            var i = new Image();
            var reader = new FileReader();

            i.onload = function(){
                var new_height = getNewHeight(i.width, i.height);
                preview.height(new_height);
            };

            reader.onload = function(e){
                var src = e.target.result;

                $('<img />', {
                    'src': src,
                }).appendTo(preview);

                $('#' + name).val(src);
                i.src = src;
            }

            preview.show();
            reader.readAsDataURL($(obj)[0].files[0]);
        }
        else
            bootbox.alert('This browser does not support FileReader.');
    }
    else
        bootbox.alert('Please select only image');
}

function deleteImage(name)
{
    bootbox.confirm('Are you sure?', function(result){
        if (result)
        {
            $('#' + name).val('');
            $('#' + name + 'File').val('');
            $('#' + name + 'Preview').hide();
            $('#' + name + 'Preview img').remove();
        }
    });
}

$(document).ready(function(){
    // Set Image

    $("#imageAvatarFile").on('change', function(){
        setImage('imageAvatar', this);
    });

    // Delete Image

    $("#deleteImageAvatarFile").on('click', function(){
        deleteImage('imageAvatar');
    });

    $('.preview').each(function(index){
        if ($(this).html().indexOf('<img') == -1)
            $(this).hide();
        else
        {
            var preview = $('#' + $(this).attr('id'));
            var inputId = $(this).attr('id').replace('Preview', '');
            var src = $('#' + inputId).val();
            var i = new Image();

            i.onload = function(){
                var new_height = getNewHeight(i.width, i.height);
                preview.height(new_height);
            };

            i.src = src;
        }
    });
});
