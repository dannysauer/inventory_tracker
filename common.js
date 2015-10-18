/*******************************************************************************
 * common scripts for inventory system
 ******************************************************************************/

/*
 * function to call when picture was added (input onchange() handler)
 * args:
 *   e: event
 */
function picture_added( e ){
    for( elem_index in e.target.form.elements ){
        elem=e.target.form.elements[elem_index];
        if(elem.type == 'submit' || elem.type == 'button'){
            elem.disabled = false;
        }
    }
    var elem = e.target;
    var preview = document.querySelector('#picture-preview');
    var file = e.target.files[0];
    preview_image( file, preview );
}

function preview_image(src, dst){
    var URL = window.URL || window.webkitURL;
    var img_url = URL.createObjectURL( src );
    dst.src = img_url;
    dst.style.visibility = 'visible';
    //URL.revokeObjectURL( img_url );
}
