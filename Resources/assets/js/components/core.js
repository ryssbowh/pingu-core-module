const Core = (() => {

	let options = {
		closeButton: $('.close')
	};

	function init(){ 
		console.log('Core initialized');
		if(options.closeButton.length){
			initCloseButton();
		}
	};

	function initCloseButton(){
		options.closeButton.click(function(e){
			if($(this).data('dismiss')){
				$(this).closest('.'+$(this).data('dismiss')).slideUp();
			}
		});
	}

	return {
		init: init
	};

})();

export default Core;