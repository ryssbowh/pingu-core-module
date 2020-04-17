import Config from './components/config';
import Logger from './components/logger';

String.prototype.trimLeft = function (charlist) {
    if (charlist === undefined) {
        charlist = "\s";
    }

    return this.replace(new RegExp("^[" + charlist + "]+"), "");
};

String.prototype.rtrim = function(s) { 
    return this.replace(new RegExp(s + "*$"),''); 
};

$(() => {
	window.Config = new Config();
    window.Logger = new Logger();
});