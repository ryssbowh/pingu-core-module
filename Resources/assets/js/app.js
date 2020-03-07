import Config from './components/config';
import Helpers from './components/helpers';

$.ajaxSetup(
    {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }
);

String.prototype.trimLeft = function (charlist) {
    if (charlist === undefined) {
        charlist = "\s";
    }

    return this.replace(new RegExp("^[" + charlist + "]+"), "");
};

$(() => {
	window.Config = new Config();
    window.Helpers = new Helpers();
});