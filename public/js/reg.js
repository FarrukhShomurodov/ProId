const inputs = document.querySelectorAll('.otc input');
inputs.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        const regex = /^[0-9]$/;
        if (!regex.test(e.data)) {
            input.value = '';
            return;
        }
        if (index < inputs.length - 1 && input.value !== '') {
            inputs[index + 1].focus();
        } else if (index === inputs.length - 1 && input.value !== '') {
            input.blur();
        }
    });
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && index > 0 && input.value === '') {
            inputs[index - 1].focus();
        }
    });
});


let in1 = document.getElementById('otc-1'),
ins = document.querySelectorAll('input[type="number"]'),
	 splitNumber = function(e) {
		let data = e.data || e.target.value;
		if ( ! data ) return;
		if ( data.length === 1 ) return;

		popuNext(e.target, data);
	},
	popuNext = function(el, data) {
		el.value = data[0];
		data = data.substring(1);
		if ( el.nextElementSibling && data.length ) {
			popuNext(el.nextElementSibling, data);
		}
	};
ins.forEach(function(input) {
	input.addEventListener('keyup', function(e){
		if (e.keyCode === 16 || e.keyCode == 9 || e.keyCode == 224 || e.keyCode == 18 || e.keyCode == 17) {
			 return;
		} if ( (e.keyCode === 8 || e.keyCode === 37) && this.previousElementSibling && this.previousElementSibling.tagName === "INPUT" ) {
			this.previousElementSibling.select();
		} else if (e.keyCode !== 8 && this.nextElementSibling) {
			this.nextElementSibling.select();
		} if ( e.target.value.length > 1 ) {
			splitNumber(e);
		}
	});

	input.addEventListener('focus', function(e) {

        if ( this === in1 )

        return;

		if ( in1.value == '' ) {
			in1.focus();
		} if ( this.previousElementSibling.value == '' ) {
			this.previousElementSibling.focus();
		}
	});
});




