$(document).ready(function () {
    mediamanager();
    cms_chooser();
    toggleEntryText();
    selectAllBoxes();
    yearFilter();
    $('#openbrowser').click(function () {
        handle_uploads();
    });
});


var modalwindow = function (title) {
    var maxSize;
    $('#modal').show();
    $('#modalTitle').html(title);
    $('#modalClose').click(function () {
        $('#modal').hide(500);
    });
    $('#modalMax').click(function () {
        if (maxSize != 1) {
            var wv = $(window).width() - 20;
            var wh = $(window).height() - 20;
            $('#modal').css({
                top: 0,
                left: 0,
                marginTop: 0,
                marginLeft: 0,
                width: wv,
                height: wh
            });
            maxSize = 1;
        } else {
            maxSize = 0;
            $('#modal').css({
                top: -400,
                left: -400,
                marginTop: '25%',
                marginLeft: '50%',
                width: 790,
                height: 790
            });

        }
    });
    return false;

};

function doconfirm(message) {
    return confirm(message);
}
function str_replace(string, search, replace) {
    return string.split(search)
            .join(replace);
}
function use_this(my_obj) {

    var dd = document.getElementById('tags');
    dd.value = str_replace(dd.value, my_obj.value + ',', '');
    if (my_obj.checked) {
        dd.value += my_obj.value + ',';
    } else {
        dd.value = str_replace(dd.value, my_obj.value + ',', '');
    }
}


var selectAllBoxes = function () {
    $('#selecta').click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

};

var toggleEntryText = function () {
    var t = $('.showTeaser');
    $('#entryText').click(function () {
        if (t.css('display') == 'none') {
            t.show();
        } else {
            t.hide();
        }
    });
    return false;

};

var cms_chooser = function () {
    $('.chooser').click(function () {
        $('.chooser').removeClass('button-primary');
        /* Find DIV */
        var id = $(this).data('id');
        console.log(id);
        $(this).addClass('button-primary');
        var elem = $('.contentlist[data-cat=' + id + ']');
        $('.contentlist').hide();
        elem.show();
    });
};

var yearFilter = function () {
    $('.filterdate').change(function () {
        var month = $('select[name="month"].filterdate').val();
        var year = $('select[name="year"].filterdate').val();
        var filter = '';
        if (month != 'null') {
            filter += '[data-month=' + month + ']';
        }
        if (year != 'null') {
            filter += '[data-year=' + year + ']';
        }
        if (filter != '') {
            $('.file_icon').hide().filter(filter).show();
        } else {
            $('.file_icon').show();
        }
    });
};

var mediamanager = function () {
    $('.file_icon').click(function () {
        var theImage = $(this).data('image');
        var imageID = $(this).data('id');

        $.post({
            data: {
                ajax: 1,
                imageID: imageID,
                image: theImage
            },
            url: WEBROOT + 'admin/filer/preview'
        }).done(function (data) {
            $('#popup').html(data);
            modalwindow('Bildvorschau');
        });
    });
};

function handle_uploads() {

    modalwindow('Dateimanager');

    $.post({
        data: {ajax: 1},
        url: WEBROOT + 'admin/filer',
    }).done(function (data) {
        if (typeof (data) != undefined) {
            $('#popup').html(data);
            $('.file_icon').click(function () {
                var theImage = $(this).data('image');

                $('#ogimage').val(theImage);
                $('#modal').hide();

                $('#previewImage').attr({
                    src: WEBROOT + 'thumb/Img/400/300/' + theImage
                });
            });

        }
    });
}

window.addEventListener('load', function () {
    var allimages = document.getElementsByTagName('img');
    for (var i = 0; i < allimages.length; i++) {
        if (allimages[i].getAttribute('data-src')) {
            allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
        }
    }
}, false);
   