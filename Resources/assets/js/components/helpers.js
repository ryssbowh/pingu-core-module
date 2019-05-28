$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

String.prototype.trimLeft = function(charlist) {
  if (charlist === undefined)
    charlist = "\s";

  return this.replace(new RegExp("^[" + charlist + "]+"), "");
};

export function ajax(url, data, type = 'POST'){
    $('body').css('cursor', 'wait');
	return $.ajax({
        type: type,
        url: url,
        data: data,
        dataType: 'json' 
	}).fail(function(data){
        let message = "%cAjax call failed : \nStatus: " + data.status;
        if(data.responseJSON.message){
            message += "\nMessage : " + data.responseJSON.message;
        }
        if(data.responseJSON.exception){
            message += "\nException : " + data.responseJSON.exception;
        }
        console.log(message,"color:red");
        $('body').css('cursor', 'initial');
	}).done(function(){
        $('body').css('cursor', 'initial');
    });
}

export function put(url, data = {}){
    data._method = 'PUT';
    return ajax(url, data, 'POST');
}

export function _delete(url, data = {}){
    data._method = 'DELETE';
    return ajax(url, data, 'POST');
}

export function patch(url, data = {}){
    data._method = 'PATCH';
    return ajax(url, data, 'POST');
}

export function post(url, data = {}){
    return ajax(url, data, 'POST');
}

export function get(url, data = {}){
    return ajax(url, data, 'GET');
}

export function logError(message){
    console.log("%c"+message, "color:red");
}

export function replaceUriSlugs(uri, replacements){
    let match = uri.match(/^.*\{([a-zA-Z_]+)\}.*$/);
    $.each(replacements, function(i, replacement){
        uri = uri.replace('{'+match[i+1]+'}', replacement);
    });
    return uri;
}