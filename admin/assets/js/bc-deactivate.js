document.addEventListener("DOMContentLoaded",(()=>{const t=document.createElement("style");t.textContent="\n\t\t#bc-confirm-modal{\n\t\t\tdisplay: flex;\n\t\t\tjustify-content: center;\n\t\t\talign-items: center;\n\t\t\twidth: 100%;\n\t\t\theight: 100%;\n\t\t\tposition: fixed;\n\t\t\ttop: 0;\n\t\t\tleft: 0;\n\t\t}\n\t\t.bc-confirm-modal-content{\n\t\t\tdisplay: flex;\n\t\t\tflex-direction: column;\n\t\t\twidth: 480px;\n\t\t\tmax-height: 450px;\n\t\t\toverflow-y: auto;\n\t\t\tbackground: #fff;\n\t\t\tcolor: #000;\n\t\t\tpadding: 30px;\n\t\t\tborder-radius: 8px;\n\t\t\tbox-shadow: rgba(14, 63, 126, 0.06) 0px 0px 0px 1px, rgba(42, 51, 70, 0.03) 0px 1px 1px -0.5px, rgba(42, 51, 70, 0.04) 0px 2px 2px -1px, rgba(42, 51, 70, 0.04) 0px 3px 3px -1.5px, rgba(42, 51, 70, 0.03) 0px 5px 5px -2.5px, rgba(42, 51, 70, 0.03) 0px 10px 10px -5px, rgba(42, 51, 70, 0.03) 0px 24px 24px -8px;\n\t\t}\n\t\t.bc-confirm-modal-content p{\n\t\t\tmargin-top: 0 !important;\n\t\t\tmargin-top: 10px;\n\t\t}\n\t\t.bc-code-snippet {\n\t\t\tdisplay: block;\n\t\t\tbackground-color: #efefef;\n\t\t\tborder: 1px solid #ccc;\n\t\t\tpadding: 10px;\n\t\t\tmargin: 10px 0;\n\t\t\twhite-space: pre; /* Keep formatting and indentation */\n\t\t\toverflow-x: auto; /* Enable horizontal scrolling if needed */\n\t\t\tfont-family: monospace; /* Use a monospace font */\n\t\t}\n\t\t.bc-actions {\n\t\t\tdisplay: flex;\n\t\t\tjustify-content: space-between;\n\t\t\talign-items: center;\n\t\t\twidth: 100%;\n\t\t\tmargin-top: 20px;\n\t\t}\n\t\t.bc-actions button {\n\t\t\twidth: fit-content;\n\t\t\tmin-width: 60px;\n\t\t}\n\t",document.head.appendChild(t);document.body.insertAdjacentHTML("beforeend","\n        <div id=\"bc-confirm-modal\" class=\"bc-confirm-modal\" style=\"display: none\">\n            <div class=\"bc-confirm-modal-content\">\n                <h2>Important Instructions for Core Framework</h2>\n                <p>If you choose \"proceed and delete all\", all Core Framework Classes and Variables will be deleted. If you choose \"proceed and keep\", all Core Framework classes and variables will still be available in the Builder, but they will no longer automatically sync with Core.</p>\n\t\t\t\t<p>Please add the following code to your <code>functions.php</code> file to ensure that the existing Core Framework continues to work in both Builder and Frontend.</p>\n\t\t\t\t<p>This code enqueues the Core Framework stylesheet into the Builder Canvas and Frontend, even if you no longer have Core Framework installed.</p>\n                <code id=\"bc-code-box\" class=\"bc-code-snippet\">\nfunction bc_enqueue_core_stylesheet_in_frontend() {\t\n\tif ( ! function_exists('CoreFramework') ) {\n\t\t$file_path = WP_CONTENT_DIR . '/uploads/brickscodes/core_framework.css';\n\t\t$file_url = content_url('uploads/brickscodes/core_framework.css');\n\t\tif (file_exists($file_path)) {\n\t\t\twp_enqueue_style('bc-core-stylesheet', $file_url, [], filemtime($file_path), 'all');\n\t\t}\n\t}\n}\t\t\t\nadd_action('wp_enqueue_scripts', 'bc_enqueue_core_stylesheet_in_frontend', 10, 1);\n\t\t\t\t\nfunction bc_enqueue_core_stylesheet_in_builder() {\n\t$theme = wp_get_theme();\n\tif ( ('Bricks' != $theme->name && 'Bricks' != $theme->parent_theme) && ! function_exists( 'bricks_is_builder' ) ) {\n\t\treturn;\n\t}\n\tif (bricks_is_builder_iframe()) {\n\t\t$file_path = WP_CONTENT_DIR . '/uploads/brickscodes/core_framework.css';\n\t\t$stylesheet = '';\n\t\t\n\t\tif (is_readable($file_path)) {\t\n\t\t\t$stylesheet = file_get_contents($file_path); \t\t\n\t\t\tif ($stylesheet === false) {\n\t\t\t\terror_log(\"Failed to read the stylesheet from: $file_path\");\n\t\t\t\treturn array();\n\t\t\t}\n\t\t} else {\n\t\t\terror_log(\"File not found or unreadable: $file_path\");\n\t\t\treturn array();\n\t\t}\n\t\t\t\n\t\tif ( !empty($stylesheet) ) {\n\t\t\techo '&lt;style&gt;' . $stylesheet . '&lt;/style&gt;';\n\t\t}\n\t}\n}\t\nadd_action('wp_footer', 'bc_enqueue_core_stylesheet_in_builder', 999, 1);\n                </code>\n\t\t\t\t<div class=\"bc-actions\">\n\t\t\t\t\t<button id=\"bc-copy-code\">Copy to Clipboard</button>\n\t\t\t\t\t<div>\n\t\t\t\t\t\t<button id=\"bc-confirm-modal-close\">Cancel</button>\n\t\t\t\t\t\t<button id=\"bc-keep-core\">Proceed and keep</button>\n\t\t\t\t\t\t<button id=\"bc-delete-core\">Proceed and delete all</button>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\t\n            </div>\n        </div>\n    ");const e=document.getElementById("deactivate-brickscodes"),n=e.getAttribute("href");e.addEventListener("click",(t=>{t.preventDefault(),document.getElementById("bc-confirm-modal").style.display="flex"}));document.getElementById("bc-confirm-modal-close").addEventListener("click",(t=>{t.stopImmediatePropagation(),document.getElementById("bc-confirm-modal").style.display="none"}));const o=document.getElementById("bc-keep-core"),a=document.getElementById("bc-delete-core");o.addEventListener("click",(t=>{t.stopImmediatePropagation(),t.target.setAttribute("disabled","disabled"),a.setAttribute("disabled","disabled");const e={action:"bc_import_core_classes_variables",nonce:bc_deactivate_ajax.nonce},o=Object.keys(e).map((t=>encodeURIComponent(t)+"="+encodeURIComponent(e[t]))).join("&");fetch(bc_deactivate_ajax.bc_deactivate_ajax_url,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:o}).then((t=>t.json())).then((t=>{t.success?(document.getElementById("bc-confirm-modal").style.display="none",n&&(window.location.href=n),console.log(t.data.message)):console.log(t.data.message)})).catch((t=>{console.error("Error:",t)}))})),a.addEventListener("click",(t=>{t.stopImmediatePropagation(),t.target.setAttribute("disabled","disabled"),o.setAttribute("disabled","disabled");const e={action:"bc_deactivate_plugin",nonce:bc_deactivate_ajax.nonce,data:"delete"},a=Object.keys(e).map((t=>encodeURIComponent(t)+"="+encodeURIComponent(e[t]))).join("&");fetch(bc_deactivate_ajax.bc_deactivate_ajax_url,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:a}).then((t=>t.json())).then((t=>{t.success?(document.getElementById("bc-confirm-modal").style.display="none",n&&(window.location.href=n),console.log(t.data.message)):console.log(t.data.message)})).catch((t=>{console.error("Error:",t)}))}));document.getElementById("bc-copy-code").addEventListener("click",(t=>{t.stopImmediatePropagation();const e=document.getElementById("bc-code-box"),n=document.createRange();n.selectNode(e);const o=window.getSelection();o.removeAllRanges(),o.addRange(n);try{const t=document.execCommand("copy");alert(t?"Code copied to clipboard!":"Unable to copy code")}catch(t){alert("Oops, unable to copy")}o.removeAllRanges()}))}));
/*
document.addEventListener('DOMContentLoaded', () => {
	const style = document.createElement('style');
	style.textContent = `
		#bc-confirm-modal{
			display: flex;
			justify-content: center;
			align-items: center;
			width: 100%;
			height: 100%;
			position: fixed;
			top: 0;
			left: 0;
		}
		.bc-confirm-modal-content{
			display: flex;
			flex-direction: column;
			width: 480px;
			max-height: 450px;
			overflow-y: auto;
			background: #fff;
			color: #000;
			padding: 30px;
			border-radius: 8px;
			box-shadow: rgba(14, 63, 126, 0.06) 0px 0px 0px 1px, rgba(42, 51, 70, 0.03) 0px 1px 1px -0.5px, rgba(42, 51, 70, 0.04) 0px 2px 2px -1px, rgba(42, 51, 70, 0.04) 0px 3px 3px -1.5px, rgba(42, 51, 70, 0.03) 0px 5px 5px -2.5px, rgba(42, 51, 70, 0.03) 0px 10px 10px -5px, rgba(42, 51, 70, 0.03) 0px 24px 24px -8px;
		}
		.bc-confirm-modal-content p{
			margin-top: 0 !important;
			margin-top: 10px;
		}
		.bc-code-snippet {
			display: block;
			background-color: #efefef;
			border: 1px solid #ccc;
			padding: 10px;
			margin: 10px 0;
			white-space: pre; 
			overflow-x: auto;
			font-family: monospace;
		}
		.bc-actions {
			display: flex;
			justify-content: space-between;
			align-items: center;
			width: 100%;
			margin-top: 20px;
		}
		.bc-actions button {
			width: fit-content;
			min-width: 60px;
		}
	`;
    document.head.appendChild(style);
	
	var modalHtml = `
        <div id="bc-confirm-modal" class="bc-confirm-modal" style="display: none">
            <div class="bc-confirm-modal-content">
                <h2>Important Instructions for Core Framework</h2>
                <p>If you choose "proceed and delete all", all Core Framework Classes and Variables will be deleted. If you choose "proceed and keep", all Core Framework classes and variables will still be available in the Builder, but they will no longer automatically sync with Core.</p>
				<p>Please add the following code to your <code>functions.php</code> file to ensure that the existing Core Framework continues to work in both Builder and Frontend.</p>
				<p>This code enqueues the Core Framework stylesheet into the Builder Canvas and Frontend, even if you no longer have Core Framework installed.</p>
                <code id="bc-code-box" class="bc-code-snippet">
function bc_enqueue_core_stylesheet_in_frontend() {	
	if ( ! function_exists('CoreFramework') ) {
		$file_path = WP_CONTENT_DIR . '/uploads/brickscodes/core_framework.css';
		$file_url = content_url('uploads/brickscodes/core_framework.css');
		if (file_exists($file_path)) {
			wp_enqueue_style('bc-core-stylesheet', $file_url, [], filemtime($file_path), 'all');
		}
	}
}			
add_action('wp_enqueue_scripts', 'bc_enqueue_core_stylesheet_in_frontend', 10, 1);
				
function bc_enqueue_core_stylesheet_in_builder() {
	$theme = wp_get_theme();
	if ( ('Bricks' != $theme->name && 'Bricks' != $theme->parent_theme) && ! function_exists( 'bricks_is_builder' ) ) {
		return;
	}
	if (bricks_is_builder_iframe()) {
		$file_path = WP_CONTENT_DIR . '/uploads/brickscodes/core_framework.css';
		$stylesheet = '';
		
		if (is_readable($file_path)) {	
			$stylesheet = file_get_contents($file_path); 		
			if ($stylesheet === false) {
				error_log("Failed to read the stylesheet from: $file_path");
				return array();
			}
		} else {
			error_log("File not found or unreadable: $file_path");
			return array();
		}
			
		if ( !empty($stylesheet) ) {
			echo '&lt;style&gt;' . $stylesheet . '&lt;/style&gt;';
		}
	}
}	
add_action('wp_footer', 'bc_enqueue_core_stylesheet_in_builder', 999, 1);
                </code>
				<div class="bc-actions">
					<button id="bc-copy-code">Copy to Clipboard</button>
					<div>
						<button id="bc-confirm-modal-close">Cancel</button>
						<button id="bc-keep-core">Proceed and keep</button>
						<button id="bc-delete-core">Proceed and delete all</button>
					</div>
				</div>	
            </div>
        </div>
    `;
	
	document.body.insertAdjacentHTML('beforeend', modalHtml);
	
	const deactivateLink = document.getElementById('deactivate-brickscodes');
	const deactivateUrl = deactivateLink.getAttribute('href');
	deactivateLink.addEventListener('click', (e) => {
		e.preventDefault();
		document.getElementById('bc-confirm-modal').style.display = 'flex';
	});
	
	const modalCloseBtn = document.getElementById('bc-confirm-modal-close');
	modalCloseBtn.addEventListener('click', (e) => {
		e.stopImmediatePropagation();
		document.getElementById('bc-confirm-modal').style.display = 'none';
	});
	
	const modalProceedKeepBtn = document.getElementById('bc-keep-core');
	const modalProceedDeleteBtn = document.getElementById('bc-delete-core');
	
	modalProceedKeepBtn.addEventListener('click', (e) => {	
		e.stopImmediatePropagation();
		e.target.setAttribute('disabled', 'disabled');
		modalProceedDeleteBtn.setAttribute('disabled', 'disabled');
		const data = {
			action: 'bc_import_core_classes_variables',
			nonce: bc_deactivate_ajax.nonce,
		};

		// Convert the data object to a URL-encoded string
		const formBody = Object.keys(data)
			.map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key]))
			.join('&');

		fetch(bc_deactivate_ajax.bc_deactivate_ajax_url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded', // Set content type to URL-encoded
			},
			body: formBody, // Use URL-encoded string
		})
		.then(response => response.json()) // Parse the JSON response
		.then(response => {
			if (response.success) {
				document.getElementById('bc-confirm-modal').style.display = 'none';
				if (deactivateUrl) {
					window.location.href = deactivateUrl;
				}
				console.log(response.data.message);
			} else {
				console.log(response.data.message);
			}
		})
		.catch(error => {
			console.error('Error:', error);
		});
		
	});
	
	modalProceedDeleteBtn.addEventListener('click', (e) => {
		e.stopImmediatePropagation();
		e.target.setAttribute('disabled', 'disabled');
		modalProceedKeepBtn.setAttribute('disabled', 'disabled');
		const data = {
			action: 'bc_deactivate_plugin',
			nonce: bc_deactivate_ajax.nonce,
			data: 'delete'
		};
		
		// Convert the data object to a URL-encoded string
		const formBody = Object.keys(data)
			.map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key]))
			.join('&');

		fetch(bc_deactivate_ajax.bc_deactivate_ajax_url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded', // Set content type to URL-encoded
			},
			body: formBody, // Use URL-encoded string
		})
		.then(response => response.json()) // Parse the JSON response
		.then(response => {
			if (response.success) {
				document.getElementById('bc-confirm-modal').style.display = 'none';
				if (deactivateUrl) {
					window.location.href = deactivateUrl;
				}
				console.log(response.data.message);
			} else {
				console.log(response.data.message);
			}
		})
		.catch(error => {
			console.error('Error:', error);
		});
	});
	
	const copyBtn = document.getElementById('bc-copy-code');
	copyBtn.addEventListener('click', (e) => {
		e.stopImmediatePropagation();
		const codeElement = document.getElementById('bc-code-box');
        const range = document.createRange();
        range.selectNode(codeElement);
        const selection = window.getSelection();
        selection.removeAllRanges(); // Clear current selection
        selection.addRange(range); // Select the code

        try {
            const successful = document.execCommand('copy');
            const msg = successful ? 'Code copied to clipboard!' : 'Unable to copy code';
            alert(msg);
        } catch (err) {
            alert('Oops, unable to copy');
        }

        selection.removeAllRanges(); // Deselect the code
	});

});
*/