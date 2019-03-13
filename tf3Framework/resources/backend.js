var tinymce = require('tinymce/tinymce');
require('@fortawesome/fontawesome-free/js/all.min.js');
require('tinymce/themes/silver/theme');
require('tinymce/plugins/paste');
require('tinymce/plugins/wordcount');
require('tinymce/plugins/code');
require('tinymce/plugins/link');
require('tinymce/plugins/image');
tinymce.init({'selector': '.ckeditor','branding': false,'relative_urls': false,'height': '50vh','plugins': ['paste', 'link', 'wordcount', 'code', 'image']},);

    class Administration {
    constructor() {
        this.tabbed_navi();
        this.confirmation();
        this.imagePreloader();
        this.thumbnail();
        this.usetag();
    };

    changevalue(obj) {
        var str = obj.value;
        var tagsfield = document.getElementById('tags');
        var old = '';
        if(obj.checked) {
            old = tagsfield.value;
            old += str+',';
            tagsfield.value = old;
        }
        else {
            old = tagsfield.value.replace(str+',','');
            tagsfield.value = old;
        }
    };

    usetag() {
        var self = this;
        var selector = document.getElementsByClassName('useTag');
        console.log(selector);
        if(selector) {
            for(var x = 0; x < selector.length; x++) {
                selector[x].onclick = function() {
                    self.changevalue(this);
                }
            }
        }
    };


    tabbed_navi() {
        var chooser = document.getElementsByClassName('chooser');
        if (chooser) {
            for (var x = 0; x < chooser.length; x++) {
                chooser[x].onclick = function() {
                    var category = this.getAttribute('data-id');
                    var contentlists = document.getElementsByClassName('contentlist');
                    // Hide all of them
                    for (var y = 0; y < contentlists.length; y++) {
                        var contentlistCategory = contentlists[y].getAttribute('data-cat');
                        if (category == contentlistCategory) {
                            contentlists[y].style.display = 'block';
                        } else {
                            contentlists[y].style.display = 'none';
                        }
                    }
                    return false;
                }
            }
        }
    };
    imagePreloader() {
        var allimages = document.getElementsByTagName('img');
        for (var i = 0; i < allimages.length; i++) {
            if (allimages[i].getAttribute('data-src')) {
                allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
            }
        }
    };
    confirmation() {
        var confirmationButtons = document.getElementsByClassName('confirmation');
        if (confirmationButtons) {
            for (var x = 0; x < confirmationButtons.length; x++) {
                confirmationButtons[x].onclick = function() {
                    return window.confirm('Wirklich löschen?');
                }
            }
        }
    };
    thumbnail() {
        var thumbnails = document.getElementsByClassName('thumbclick');
        var ibox = document.getElementById('url');
        if (thumbnails) {
            for (var x = 0; x < thumbnails.length; x++) {
                thumbnails[x].onclick = function() {
                    ibox.value = this.getAttribute('data-url');
                }
            }
        }
    };
}
window.addEventListener('load', function() {
    var admin = new Administration();
}, false);