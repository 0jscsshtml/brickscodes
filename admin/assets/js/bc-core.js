function initCoreFn(){!function(){const e=window.fetch;window.fetch=async function(...o){const n=o[0];if("string"==typeof n&&n.includes(bc_core_ajax.bc_core_ajax_url))return e.apply(this,o);const c=await e.apply(this,o);if(c.url.includes("/core-framework/v2/update-main"))if(c.ok)try{const e=await c.clone().json();e.success?function(){const e={action:"bc_get_core_classes_variables",nonce:bc_core_ajax.nonce},o=Object.keys(e).map((o=>encodeURIComponent(o)+"="+encodeURIComponent(e[o]))).join("&");fetch(bc_core_ajax.bc_core_ajax_url,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:o}).then((e=>e.json())).then((e=>{e.success,console.log(e.data.message)})).catch((e=>{console.error("Error:",e)}))}():console.error("Data was not successful:",e)}catch(e){console.error("Error parsing JSON response:",e)}else console.error("Response was not OK:",c.status);return c}}()}window.addEventListener("load",(()=>{initCoreFn()}));
/*
window.addEventListener("load", () => {
	initCoreFn();
});

function initCoreFn() {	
	(function() {
		const originalFetch = window.fetch;
		window.fetch = async function(...args) {
			const requestUrl = args[0]; // Get the request URL
			// Check if the request is not your custom AJAX call, so we don't intercept it
			if (typeof requestUrl === 'string' && requestUrl.includes(bc_core_ajax.bc_core_ajax_url)) {
				return originalFetch.apply(this, args); // Bypass fetch interception for your AJAX call
			}
			
			const response = await originalFetch.apply(this, args);

			if (response.url.includes('/core-framework/v2/update-main')) {
				if (response.ok) {
					try {
						const data = await response.clone().json(); // Clone the response to prevent it from being read twice
						if ( data.success ) {
							ajaxGetCoreClasses();
						} else {
							console.error("Data was not successful:", data);
						}
					} catch (error) {
						console.error("Error parsing JSON response:", error);
					}
				} else {
					console.error("Response was not OK:", response.status);
				}
			}

			return response; // Ensure to return the original response
		};
	})();
		
	function ajaxGetCoreClasses() {
		const data = {
			action: 'bc_get_core_classes_variables',
			nonce: bc_core_ajax.nonce,
		};

		const formBody = Object.keys(data)
			.map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key]))
			.join('&');

		fetch(bc_core_ajax.bc_core_ajax_url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded', // Set content type to URL-encoded
			},
			body: formBody, // Use URL-encoded string
		})
		.then(response => response.json()) // Parse the JSON response
		.then(response => {
			if (response.success) {
				console.log(response.data.message);
			} else {
				console.log(response.data.message);
			}
		})
		.catch(error => {
			console.error('Error:', error);
		});
	}
}
*/