document.addEventListener('DOMContentLoaded', () => {
	const	bcOptions = bc_acf_options_page.optionSettings,
			preloader = document.getElementById('bricks-preloader');
			
	// Builder preloader background color
	if (bcOptions?.bc_builder_tweaks?.bc_preloader_background) {		
		preloader.style.backgroundColor = bcOptions.bc_builder_tweaks.bc_preloader_background;
		preloader.querySelector('.title img').style.filter = 'invert(75%)';
	}
	
    window.addEventListener("load", () => {
        initBcBuilderApp()
    });
});

function initBcBuilderApp() {
	const 	vueState 			= document.querySelector('.brx-body').__vue_app__.config.globalProperties.$_state,
			vueGlobal 			= document.querySelector('.brx-body').__vue_app__.config.globalProperties,
			toolbar				= document.getElementById('bricks-toolbar'),
			mainPanel			= document.querySelector('.brx-body.main > #bricks-panel'),
			innerPanel 			= document.getElementById('bricks-panel-inner'),
			eleClassesPanel		= document.getElementById('bricks-panel-element-classes'),
			elementCatPanel 	= document.getElementById('bricks-panel-elements'),	
			previewPanel 		= document.getElementById('bricks-preview'),
			leftPanelDragHandle = document.querySelector('.brx-body.main > #bricks-panel > #bricks-panel-resizable'),
			structurePanel 		= document.getElementById('bricks-structure'),
			iframe            	= document.getElementById('bricks-builder-iframe'),
			iframeDocument    	= iframe.contentDocument || iframe.contentWindow.document,
			loopEles			= ['block', 'container', 'div', 'section', 'accordion', 'accordion-nested', 'slider', 'slider-nested', 'tabs-nested'];

	const 	saveQuerySvg 	= '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="bricks-svg"><path d="M21.75 23.25H2.25a1.5 1.5 0 0 1 -1.5 -1.5V7.243a3 3 0 0 1 0.879 -2.121l3.492 -3.493A3 3 0 0 1 7.243 0.75H21.75a1.5 1.5 0 0 1 1.5 1.5v19.5a1.5 1.5 0 0 1 -1.5 1.5Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="M9.75 12.75a3 3 0 1 0 6 0 3 3 0 1 0 -6 0Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="m12.75 20.25 6.75 0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="M8.25 0.75v3a1.5 1.5 0 0 0 1.5 1.5h7.5a1.5 1.5 0 0 0 1.5 -1.5v-3" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>',
			openQuerySvg	= '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="bricks-svg"><path d="M20.25 9.75v-3a1.5 1.5 0 0 0 -1.5 -1.5H8.25v-1.5a1.5 1.5 0 0 0 -1.5 -1.5h-4.5a1.5 1.5 0 0 0 -1.5 1.5v16.3a1.7 1.7 0 0 0 3.336 0.438l2.351 -9.657A1.5 1.5 0 0 1 7.879 9.75H21.75a1.5 1.5 0 0 1 1.45 1.886l-2.2 9a1.5 1.5 0 0 1 -1.45 1.114H2.447" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>',
			expandAll		= '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="bricks-svg"><path d="m9.75 14.248 -9 9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="m23.25 7.498 0 -6.75 -6.75 0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="m0.75 16.498 0 6.75 6.75 0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="m23.25 0.748 -9 9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>',
			collapseAll		= '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="bricks-svg"><path d="m23.25 0.748 -9 9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="m9.75 20.998 0 -6.75 -6.75 0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="m14.25 2.998 0 6.75 6.75 0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="m9.75 14.248 -9 9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>';
	
	let eleClasses, activeIframeNode, cssVarInput, cssVarInputValue, styleTagId;	
    let bcOptions, panelIsResizing, modelObj, bcModal, modalHeader, modalFooter, modalBody;
	let querySaveModal, queryRecordModal, queryArgsModal, querySettingsEditor, queriesArgs, queriesTags;
	let bcCoreOptions, cfClasses, cfVariables, cfVariablesPair, cfVariablesModal, mainAccordHeaders, mainAccordItems, classHovered;

    if (typeof bc_acf_options_page !== 'undefined') {
        bcOptions = bc_acf_options_page.optionSettings;
    }

	if (typeof coreFramework !== 'undefined') {
		bcCoreOptions = coreFramework;
    }
	
    const newElesBar = [
        {name: 'action-bar', innerText: '', cls: '', label: 'Switch to Action Bar', position: 'left', icon: 'ti-widget' },
		{name: 'section', innerText: '', cls: '', label: 'Add new Section', position: 'left', icon: 'ti-layout-accordion-separated'},
		{name: 'block', innerText: '', cls: '', label: 'Add new Block', position: 'left', icon: 'ti-layout-width-full'},
		{name: 'div', innerText: '', cls: '', label: 'Add new Div', position: 'left', icon: 'ti-layout-width-default-alt'},
		{name: 'heading', innerText: '', cls: '', label: 'Add new Heading', position: 'left', icon: 'ti-text'},
		{name: 'text-basic', innerText: '', cls: '', label: 'Add new Basic Text', position: 'left', icon: 'ti-align-justify'},
		{name: 'text', innerText: '', cls: '', label: 'Add new Rich Text', position: 'left', icon: 'ti-align-left'},
		{name: 'text-link', innerText: '', cls: '', label: 'Add new Text Link', position: 'left', icon: 'ti-link'},
		{name: 'button', innerText: '', cls: '', label: 'Add new Button', position: 'left', icon: 'ti-control-stop'},
		{name: 'icon', innerText: '', cls: '', label: 'Add new Icon', position: 'left', icon: 'ti-star'},
		{name: 'image', innerText: '', cls: '', label: 'Add new Image', position: 'left', icon: 'ti-image'},
		{name: 'list', innerText: '', cls: '', label: 'Add new List', position: 'left', icon: 'ti-list'},
		{name: 'code', innerText: '', cls: '', label: 'Add new Code', position: 'left', icon: 'ion-ios-code'},
		{name: 'template', innerText: '', cls: '', label: 'Add new Template', position: 'left', icon: 'ti-layers'},
		{name: 'shortcode', innerText: '', cls: '', label: 'Add new Shortcode', position: 'left', icon: 'ti-shortcode'},
    ];

    const newActionBar = [
        { name: 'new-element-bar', innerText: '', cls: 'hide', label: 'Switch to Add Element Bar', position: 'left', icon: 'ti-widget' },
        { name: 'hide-in-frontend', innerText: 'HIF', cls: 'hide', label: 'Highlight Elements with Hide in Frontend', position: 'left', icon: '' },
        { name: 'hide-in-canvas', innerText: 'HIC', cls: 'hide', label: 'Highlight Elements with Hide in Canvas', position: 'left', icon: '' },
		{ name: 'expand-all-childrens', innerText: '', cls: 'show', label: 'Expand All Childrens', position: 'left', icon: '' },
		{ name: 'collapse-all-childrens', innerText: '', cls: 'hide', label: 'Collapse All Childrens', position: 'left', icon: '' },
    ];
		
	// new element shortcut bar at structure panel 
	if (vueState.fullAccess && bcOptions?.bc_builder_tweaks?.bc_structure_panel_shortcut) {
		structurePanel.setAttribute('data-shortcut-bar', '');
		const ul = document.createElement('ul');
		ul.id = 'bc-right-shortcut-bar';
		newElesBar.forEach(ele => {
			const iconEle = createDomElement('li', {'data-element-name': ele.name, textContent: ele.innerText, classList: [], 'data-balloon': ele.label, 'data-balloon-pos': ele.position}, [
				createDomElement('i', {classList: [ele.icon]})
			]);
			ul.appendChild(iconEle);
		});
		structurePanel.querySelector('main.panel-content').prepend(ul);
	}
	
	// new action shortcut bar for hide-in-canvas, hide-in-frontend at structure panel
    if (vueState.fullAccess && bcOptions?.bc_builder_tweaks?.bc_show_hide_elements) {
		iframeDocument.body.setAttribute('data-show-hide-element', '');
        structurePanel.setAttribute('data-shortcut-bar', '');
		let ulEle;
		if ( !document.getElementById('bc-right-shortcut-bar') ) {
			ulEle = document.createElement('ul');
			ulEle.id = 'bc-right-shortcut-bar';
			structurePanel.querySelector('main.panel-content').prepend(ulEle);
		}
		ulEle = document.getElementById('bc-right-shortcut-bar');	
		const hasNewEleBar = ulEle.querySelector('li[data-element-name="action-bar"]');	
		newActionBar.forEach((ele, index) => {
			if ( index < 3 ) {
				const classList = hasNewEleBar ? [ele.cls] : [];
				const iconEle = createDomElement('li', {'data-element-name': ele.name, textContent: ele.innerText, classList, 'data-balloon': ele.label, 'data-balloon-pos': ele.position}, [
					createDomElement('i')
				]);		
				if ( ele.icon ) {
					iconEle.children[0].classList.add(ele.icon);
				}
				ulEle.appendChild(iconEle);
			}
        });
    }
	
	// new action shortcut bar for expand all children at structure panel 
	if (vueState.fullAccess && bcOptions?.bc_builder_tweaks?.bc_expand_collapse_children) {
		structurePanel.setAttribute('data-shortcut-bar', '');
		let ulEle;
		if ( !document.getElementById('bc-right-shortcut-bar') ) {
			ulEle = document.createElement('ul');
			ulEle.id = 'bc-right-shortcut-bar';
			structurePanel.querySelector('main.panel-content').prepend(ulEle);
		}
		ulEle = document.getElementById('bc-right-shortcut-bar');
		const hasNewEleBar = ulEle.querySelector('li[data-element-name="action-bar"]');	
		const liEle = createDomElement('li', {'data-element-name': 'toggle-children', classList: hasNewEleBar ? ['hide'] : []});
		newActionBar.forEach((ele, index) => {
			if ( index >= 3 ) {
				const svgIcon = index === 3 ? expandAll : collapseAll;
				const iconEle = createDomElement('span', {'data-element-name': ele.name, textContent: ele.innerText, classList: [ele.cls], 'data-balloon': ele.label, 'data-balloon-pos': ele.position}, `${svgIcon}`);
				liEle.appendChild(iconEle);
			}
        });
		ulEle.appendChild(liEle);
	}
	
	// brickscodes modal
	const modalHeaderEle = [
		{cls: ['bc-dock-left', 'hide'], label: 'Dock to left', position: 'bottom-left', icon: 'ti-arrow-left'},
		{cls: ['bc-dock-right', 'hide'], label: 'Dock to right', position: 'bottom-right', icon: 'ti-arrow-right'},
	]
	const createModal = createDomElement('div', { id: 'bc-modal', classList: ['hide'] }, [
		createDomElement('div', { id: 'bc-modal-content' }, [
			createDomElement('div', { classList: ['bc-modal-header', 'hide'] }, [
				createDomElement('li', { classList: modalHeaderEle[0].cls, 'data-balloon': modalHeaderEle[0].label, 'data-balloon-pos': modalHeaderEle[0].position }, [
					createDomElement('i', {classList: [modalHeaderEle[0].icon]})
				]),
				createDomElement('li', { classList: modalHeaderEle[1].cls, 'data-balloon': modalHeaderEle[1].label, 'data-balloon-pos': modalHeaderEle[1].position }, [
					createDomElement('i', {classList: [modalHeaderEle[1].icon]})
				])
			]),
			createDomElement('div', { classList: ['bc-modal-body'] }),
			createDomElement('div', { classList: ['bc-modal-footer', 'hide'] })
		]),
	]);
	document.body.children[0].appendChild(createModal);
	bcModal 	= document.getElementById('bc-modal');
	modalHeader = bcModal.querySelector('.bc-modal-header');
	modalBody   = bcModal.querySelector('.bc-modal-body');
	modalFooter = bcModal.querySelector('.bc-modal-footer');
	document.addEventListener('keydown', docEvents);
	bcModal.addEventListener('mousedown', modalDivEvents);
	bcModal.addEventListener('click', modalDivEvents);
	
	const clearSvg = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="bricks-svg"><path d="m0.75 23.249 22.5 -22.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="M23.25 23.249 0.75 0.749" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>';
	const dropdownSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="bricks-svg"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M23.025 6.64L12.5194 17.1446C12.4512 17.2129 12.3702 17.267 12.2812 17.304C12.192 17.341 12.0964 17.36 12 17.36C11.9036 17.36 11.808 17.341 11.7188 17.304C11.6298 17.267 11.5488 17.2129 11.4806 17.1446L0.975 6.64" stroke-width="2"></path></svg>';
	const editSvg = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="bricks-svg"><path d="M22.19 1.81a3.639 3.639 0 0 0 -5.17 0.035l-14.5 14.5L0.75 23.25l6.905 -1.771 14.5 -14.5a3.637 3.637 0 0 0 0.035 -5.169Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="m16.606 2.26 5.134 5.134" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path d="m2.521 16.344 5.139 5.13" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>';
	
	// query manager modal
	if ( vueState.fullAccess && bcOptions?.bc_builder_tweaks?.bc_query_manager ) {		
		queriesArgs = bc_query_manager.queriesArgs && Object.keys(bc_query_manager.queriesArgs).length > 0 ? bc_query_manager.queriesArgs : {};
		queriesTags = bc_query_manager.queriesTags && bc_query_manager.queriesTags.length > 0 ? bc_query_manager.queriesTags : [];

		const querySave = createDomElement('div', { classList: ['bc-query-save', 'hide'] }, [
			createDomElement('p', { classList: ['bc-query-save-title'], textContent: 'Title' }),
			createDomElement('input', { type: 'text', autocomplete: 'off', spellcheck: 'off', id: 'bc-query-save-title', placeholder: 'New query name here...' }),
			createDomElement('p', { classList: ['bc-query-save-tag'], textContent: 'New Category/Tag' }),
			createDomElement('input', { type: 'text', autocomplete: 'off', spellcheck: 'off', id: 'bc-query-save-tag', placeholder: '' }),
			createDomElement('p', { classList: ['bc-query-save-tag-select'], textContent: 'Select Existing Category/Tag' }),
			createDomElement('div', { type: 'select', tabindex: 0, classList: ['tags'], 'data-control': 'select' }, [
				createInputWrapper(),
				createOptionWrapper()
			]),
			createDomElement('p', { classList: ['bc-query-save-desc'], textContent: 'Description' }),
			createDomElement('textarea', { classList: ['bc-query-save-desc'], placeholder: 'New query description here...' }),
			createDomElement('p', { textContent: 'Note: Save current query control settings as new query settings record.' }),
		]);
		
		const querySearch = createDomElement('div', { classList: ['bc-query-search', 'hide'] }, [
			createDomElement('p', { textContent: 'Filter by Category/Tag' }),
			createDomElement('div', { type: 'select', tabindex: 0, classList: ['tags'], 'data-control': 'select' }, [
				createInputWrapper(),
				createOptionWrapper()
			]),
			createDomElement('p', { textContent: 'Select from query settings records' }),
			createDomElement('div', { type: 'select', tabindex: 0, classList: ['title'], 'data-control': 'select' }, [
				createInputWrapper(),
				createOptionWrapper()
			]),	
			createDomElement('p', { textContent: 'Query Description' }),
			createDomElement('div', { classList: ['query-desc'] }, [
				createDomElement('textarea', { classList: ['bc-query-search-desc', 'disabled'] }),
				createDomElement('span', { classList: ['bricks-svg-wrapper', 'edit', 'hide'] }, `${editSvg}`),
			]),
			createDomElement('p', { textContent: 'Note:\n1) "Apply" – Choose a query settings record and click "Apply" to load its settings into the query controls.\n2) "Apply & Update" – Modify the current query settings record, including query controls, name, or description, then click "Apply & Update" to save the changes and update all instances with the same query ID across the entire site.' }),
		]);
		
		const querySettings = createDomElement('div', { classList: ['bc-query-args', 'hide'] }, [
			createDomElement('p', { textContent: 'Preview Selected Query Settings' }),
			createDomElement('textarea', { spellcheck: 'false', autocomplete: 'off' }),
			createDomElement('button', { classList: ['close-settings', 'hide'], textContent: 'Close' })
		]);
		
		modalBody = bcModal.querySelector('.bc-modal-body');
		modalBody.appendChild(querySave);
		modalBody.appendChild(querySearch);
		modalBody.appendChild(querySettings);
		
		modalFooter = bcModal.querySelector('.bc-modal-footer');
		modalFooter.appendChild(createDomElement('button', { classList: ['save', 'hide'], textContent: 'Save' }));
		modalFooter.appendChild(createDomElement('button', { classList: ['apply', 'hide'], disabled: 'disabled', textContent: 'Apply' }));
		modalFooter.appendChild(createDomElement('button', { classList: ['apply-save', 'hide'], disabled: 'disabled', textContent: 'Apply & Update' }));
		
		const queryConfirm = createDomElement('div', { classList: ['bc-query-confirmation', 'hide'] }, [
			createDomElement('p', { textContent: 'You are updating the query record with the current settings, this will update across entire site. Are you sure want to proceed?' }),
			createDomElement('button', { classList: ['update-query'], textContent: 'Yes' }),
			createDomElement('button', { classList: ['abort-update-query'], textContent: 'No' })
		]);
		bcModal.appendChild(queryConfirm);
		
		querySaveModal 		= bcModal.querySelector('.bc-query-save');
		queryRecordModal 	= bcModal.querySelector('.bc-query-search');
		queryArgsModal 		= bcModal.querySelector('.bc-query-args');
		
		if (typeof cm_settings !== 'undefined') {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
			editorSettings.codemirror = _.extend(
                {},
				editorSettings.codemirror,
				cm_settings.codemirror
			);
			querySettingsEditor = wp.codeEditor.initialize( queryArgsModal.children[1], editorSettings);
			const display = querySettingsEditor.codemirror.display;			
			display.wrapper.classList.remove('cm-s-default');
			display.wrapper.classList.add('cm-s-one-dark');
			display.wrapper.style.clipPath = 'inset(0px)';
			display.gutters.children[1].style.width = '29px';
			display.scrollbars.vert.setAttribute('tabindex', '-1');
			display.scrollbars.horiz.setAttribute('tabindex', '-1');
			display.scroller.setAttribute('tabindex', '-1');
			display.sizer.style.marginLeft = '45px';
			querySettingsEditor.codemirror.options.readOnly = true;
			querySettingsEditor.settings.codemirror.lint = false;
		}
	}

	// create core framework variables modal   
	if ( vueState.fullAccess && bcCoreOptions && bcOptions?.bc_classes_variables?.bc_core_integration ) {
		if (bcOptions?.bc_classes_variables?.bc_core_theme_toggle) {
			const moonSvg = '<svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M3.32031 11.6835C3.32031 16.6541 7.34975 20.6835 12.3203 20.6835C16.1075 20.6835 19.3483 18.3443 20.6768 15.032C19.6402 15.4486 18.5059 15.6834 17.3203 15.6834C12.3497 15.6834 8.32031 11.654 8.32031 6.68342C8.32031 5.50338 8.55165 4.36259 8.96453 3.32996C5.65605 4.66028 3.32031 7.89912 3.32031 11.6835Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g></svg>';
			const sunSvg = '<svg height="24px" width="24px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="currentColor"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g> <style type="text/css"> .st0{fill:currentColor;} </style> <g> <path class="st0" d="M255.996,127.577c-70.925,0-128.42,57.495-128.42,128.42c0,70.933,57.496,128.428,128.42,128.428 s128.428-57.495,128.428-128.428C384.424,185.072,326.921,127.577,255.996,127.577z"/> <path class="st0" d="M512,255.996c-78.109-49.042-98.052-93.51-75.065-180.932c-87.414,22.995-131.89,3.036-180.939-75.057 c-49.042,78.093-93.51,98.052-180.932,75.057C98.06,162.487,78.109,206.954,0,255.996c78.109,49.049,98.06,93.525,75.065,180.939 c87.422-22.987,131.89-3.036,180.932,75.057c49.049-78.093,93.525-98.044,180.939-75.057 C413.948,349.522,433.891,305.046,512,255.996z M255.996,423.766c-92.666,0-167.762-75.112-167.762-167.77 c0-92.65,75.096-167.762,167.762-167.762c92.65,0,167.769,75.112,167.769,167.762C423.766,348.654,348.646,423.766,255.996,423.766 z"/> </g> </g></svg>';
			const lightDarkToggle = createDomElement('li', { classList: ['core-theme-mode-toggle'], 'data-balloon': 'Core Framework Light/Dark mode preview', 'data-balloon-pos': 'bottom' }, [
				createDomElement('span', { classList: ['bricks-svg-wrapper', 'dark'] }, `${moonSvg}`),
				createDomElement('span', { classList: ['bricks-svg-wrapper', 'light', 'hide'] }, `${sunSvg}`),
			]);
			toolbar.children[0].append(lightDarkToggle);
		}

		cfClasses = bcCoreOptions.classes && bcCoreOptions.classes.length > 0 ? bcCoreOptions.classes : [];
		cfVariables = bcCoreOptions.variables && Object.keys(bcCoreOptions.variables).length > 0 ? bcCoreOptions.variables : {};
		cfVariablesPair = bcCoreOptions.variables_pair && Object.keys(bcCoreOptions.variables_pair).length > 0 ? bcCoreOptions.variables_pair : {};
		
		const variablesSelection = createDomElement('div', { type: 'select', tabindex: 0, classList: ['select-framework'], 'data-control': 'select' }, [
			createInputWrapper(),
			createOptionWrapper()
		]);
		const li = createDomElement('li', { 'data-index': 0, 'data-selected-framwork': 'coreframework' }, [
			createDomElement('span', { textContent: 'Core Framework' }),
		]);
		variablesSelection.children[0].children[0].textContent = 'Core Framework';
		variablesSelection.children[0].children[0].classList.add('input-value');
		variablesSelection.querySelector('#query-name').remove();
		variablesSelection.querySelector('ul.dropdown').appendChild(li);
		modalHeader.prepend(variablesSelection);

		const wrapper = createDomElement('div', { classList: ['bc-core-variables', 'hide'] }, [
			createDomElement('div', { classList: ['accordion', 'js-accordion'] })
		]);
		
		if (cfVariables && Object.keys(cfVariables).length > 0) {
			for (const category in cfVariables) {
				const modifiedCategory = category.replace('Styles', '');
				const accordTitle = modifiedCategory.charAt(0).toUpperCase() + modifiedCategory.slice(1);
				
				const accordHead = createDomElement('div', { classList: ['accordion__item', 'js-accordion-item'] }, [
					createDomElement('div', { classList: ['accordion-header', 'js-accordion-header'], textContent: accordTitle }),
				]);	
				const accordBody = createDomElement('div', { classList: ['accordion-body', 'js-accordion-body'] }, [
					createDomElement('div', { classList: ['accordion', 'js-accordion'], 'data-group': accordTitle }),
				]);

				const subCategories = cfVariables[category];
				for (const subCategory in subCategories) {
					const modifiedSubCategory = subCategory.replace('Styles', '');
					const accordSubTitle = modifiedSubCategory.charAt(0).toUpperCase() + modifiedSubCategory.slice(1);
					
					const accordSubHead = createDomElement('div', { classList: ['accordion__item', 'accordion__sub_item', 'js-accordion-item'] }, [
						createDomElement('div', { classList: ['accordion-header', 'js-accordion-header'], textContent: `-${accordSubTitle}` }),
					]);	
					const accordSubBody = createDomElement('div', { classList: ['accordion-body', 'js-accordion-body'] }, [
						createDomElement('ul', { classList: ['accordion-body__contents'] })
					]);
					
					const ulElement = accordSubBody.children[0];
					
					const values = subCategories[subCategory];
					values.forEach(value => {
						const variableValue = Object.keys(cfVariablesPair).length > 0 ? cfVariablesPair[`--${value}`]: '';
						const isBaseColor 	= accordTitle === 'Color' && !value.includes('-') ? { 'data-color-base': value } : {};
						const spanWidth 	= accordTitle === 'Color' && !value.includes('-') ? '100%' : '54px';
						const isColor 		= accordTitle === 'Color' ? {style: `--${value}: ${variableValue}; background-color: var(--${value}); display: block; width: ${spanWidth}; height: 26px; border-radius: 4px;`, 'data-balloon': `${value}`, 'data-balloon-pos': 'bottom'} : {textContent: `${value}`, 'data-balloon': cfVariablesPair[`--${value}`], 'data-balloon-pos': 'bottom'};
						const liElement 	= createDomElement('li', { classList: ['bc-core-variable'], 'data-variables-name': `var(--${value})`, ...isBaseColor }, [
							createDomElement('span', isColor) // Create <span> with the value
						]);

						ulElement.appendChild(liElement); // Append the new <li> to the <ul>
					});
					
					accordSubHead.appendChild(accordSubBody);
					accordBody.children[0].appendChild(accordSubHead);
					accordHead.appendChild(accordBody);
					wrapper.children[0].appendChild(accordHead);
				}
			}
			bcModal.children[0].children[1].append(wrapper);
		}
		
		cfVariablesModal = bcModal.querySelector('.bc-core-variables');
		mainAccordItems	= cfVariablesModal && cfVariablesModal.children[0] ? cfVariablesModal.children[0].children : null;
		if ( cfVariablesModal && bcOptions?.bc_builder_tweaks?.bc_preview_class_variables) {
			cfVariablesModal.addEventListener('mouseover', modalVariableEvents);
			cfVariablesModal.addEventListener('mouseout', modalVariableEvents);
			cfVariablesModal.addEventListener('mouseleave', modalVariableEvents);
		}
	}
	
	// slim left panel
	if (bcOptions?.bc_builder_tweaks?.bc_slim_left_panel) {
		const 	expandEle 		= '<li class="expand hide" data-balloon="Expand (All)" data-balloon-pos="bottom-right"><span class="bricks-svg-wrapper"><svg version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="bricks-svg"><g fill="currentColor" fill-rule="evenodd"><path d="M2,8l5.32907e-15,7.54979e-08c-4.16963e-08,-0.276142 0.223858,-0.5 0.5,-0.5h11l-2.18557e-08,8.88178e-16c0.276142,-1.20706e-08 0.5,0.223858 0.5,0.5c1.20706e-08,0.276142 -0.223858,0.5 -0.5,0.5h-11l-2.78181e-08,-3.55271e-15c-0.276142,-4.49893e-08 -0.5,-0.223858 -0.5,-0.5Zm6,-1.5l-2.18557e-08,-8.88178e-16c0.276142,1.20706e-08 0.5,-0.223858 0.5,-0.5v-4.5v0c0,-0.276142 -0.223858,-0.5 -0.5,-0.5c-0.276142,0 -0.5,0.223858 -0.5,0.5v4.5l5.32907e-15,7.54979e-08c4.16963e-08,0.276142 0.223858,0.5 0.5,0.5Z"></path><path d="M10.354,3.854l2.23014e-08,-2.2245e-08c0.195509,-0.195015 0.195909,-0.511597 0.000893739,-0.707106c-0.000297551,-0.000298304 -0.000595479,-0.000596232 -0.000893784,-0.000893784l-2,-2l4.41373e-09,4.4249e-09c-0.195015,-0.195509 -0.511597,-0.195909 -0.707106,-0.000893793c-0.000298304,0.000297551 -0.000596232,0.000595479 -0.000893784,0.000893784l-2,2l-2.1107e-09,2.1107e-09c-0.195509,0.195509 -0.195509,0.512491 4.22141e-09,0.708c0.195509,0.195509 0.512491,0.195509 0.708,-4.22141e-09l1.646,-1.647l1.646,1.647l-3.52833e-08,-3.53726e-08c0.195015,0.195509 0.511597,0.195909 0.707106,0.000893854c0.000298304,-0.000297551 0.000596233,-0.000595479 0.000893784,-0.000893784Zm-2.354,5.646h-2.18557e-08c0.276142,-1.20706e-08 0.5,0.223858 0.5,0.5v4.5v0c0,0.276142 -0.223858,0.5 -0.5,0.5c-0.276142,0 -0.5,-0.223858 -0.5,-0.5v-4.5l5.32907e-15,7.54979e-08c-4.16963e-08,-0.276142 0.223858,-0.5 0.5,-0.5Z"></path><path d="M10.354,12.146l2.23014e-08,2.2245e-08c0.195509,0.195015 0.195909,0.511597 0.000893739,0.707106c-0.000297551,0.000298304 -0.000595479,0.000596232 -0.000893784,0.000893784l-2,2l4.41373e-09,-4.4249e-09c-0.195015,0.195509 -0.511597,0.195909 -0.707106,0.000893793c-0.000298304,-0.000297551 -0.000596232,-0.000595479 -0.000893784,-0.000893784l-2,-2l-2.1107e-09,-2.1107e-09c-0.195509,-0.195509 -0.195509,-0.512491 4.22141e-09,-0.708c0.195509,-0.195509 0.512491,-0.195509 0.708,4.22141e-09l1.646,1.647l1.646,-1.647l-3.52833e-08,3.53726e-08c0.195015,-0.195509 0.511597,-0.195909 0.707106,-0.000893854c0.000298304,0.000297551 0.000596233,0.000595479 0.000893784,0.000893784Z"></path></g></svg></span></li>',
				collapseEle 	= '<li class="collapse" data-balloon="Collapse (All)" data-balloon-pos="bottom-right"><span class="bricks-svg-wrapper"><svg version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="bricks-svg"><g fill="currentColor" fill-rule="evenodd"><path d="M2,8l5.32907e-15,7.54979e-08c-4.16963e-08,-0.276142 0.223858,-0.5 0.5,-0.5h11l-2.18557e-08,8.88178e-16c0.276142,-1.20706e-08 0.5,0.223858 0.5,0.5c1.20706e-08,0.276142 -0.223858,0.5 -0.5,0.5h-11l-2.78181e-08,-3.55271e-15c-0.276142,-4.49893e-08 -0.5,-0.223858 -0.5,-0.5Zm6,-7h-2.18557e-08c0.276142,-1.20706e-08 0.5,0.223858 0.5,0.5v4.5v0c0,0.276142 -0.223858,0.5 -0.5,0.5c-0.276142,0 -0.5,-0.223858 -0.5,-0.5v-4.5l5.32907e-15,7.54979e-08c-4.16963e-08,-0.276142 0.223858,-0.5 0.5,-0.5Z"></path><path d="M10.354,3.646l2.23014e-08,2.2245e-08c0.195509,0.195015 0.195909,0.511597 0.000893739,0.707106c-0.000297551,0.000298304 -0.000595479,0.000596232 -0.000893784,0.000893784l-2,2l4.41373e-09,-4.4249e-09c-0.195015,0.195509 -0.511597,0.195909 -0.707106,0.000893793c-0.000298304,-0.000297551 -0.000596232,-0.000595479 -0.000893784,-0.000893784l-2,-2l-2.1107e-09,-2.1107e-09c-0.195509,-0.195509 -0.195509,-0.512491 4.22141e-09,-0.708c0.195509,-0.195509 0.512491,-0.195509 0.708,4.22141e-09l1.646,1.647l1.646,-1.647l-3.52833e-08,3.53726e-08c0.195015,-0.195509 0.511597,-0.195909 0.707106,-0.000893854c0.000298304,0.000297551 0.000596233,0.000595479 0.000893784,0.000893784Zm-2.354,11.354h-2.18557e-08c0.276142,1.20706e-08 0.5,-0.223858 0.5,-0.5v-4.5v0c0,-0.276142 -0.223858,-0.5 -0.5,-0.5c-0.276142,0 -0.5,0.223858 -0.5,0.5v4.5l5.32907e-15,7.54979e-08c4.16963e-08,0.276142 0.223858,0.5 0.5,0.5Z"></path><path d="M10.354,12.354l2.23014e-08,-2.2245e-08c0.195509,-0.195015 0.195909,-0.511597 0.000893739,-0.707106c-0.000297551,-0.000298304 -0.000595479,-0.000596232 -0.000893784,-0.000893784l-2,-2l4.41373e-09,4.4249e-09c-0.195015,-0.195509 -0.511597,-0.195909 -0.707106,-0.000893793c-0.000298304,0.000297551 -0.000596232,0.000595479 -0.000893784,0.000893784l-2,2l-2.1107e-09,2.1107e-09c-0.195509,0.195509 -0.195509,0.512491 4.22141e-09,0.708c0.195509,0.195509 0.512491,0.195509 0.708,-4.22141e-09l1.646,-1.647l1.646,1.647l-3.52833e-08,-3.53726e-08c0.195015,0.195509 0.511597,0.195909 0.707106,0.000893854c0.000298304,-0.000297551 0.000596233,-0.000595479 0.000893784,-0.000893784Z"></path></g></svg></span></li>',	
				eleCatHeader 	= innerPanel.querySelector('#bricks-panel-header'),
				toggleBtn 		= createDomElement('div', { id: 'bc-category-toggle' }, `${expandEle}${collapseEle}`);
		innerPanel.setAttribute('data-slim-panel', '');
		eleCatHeader.append(toggleBtn);
	}

	if ( (bcOptions?.bc_builder_tweaks?.bc_preview_class_variables) || (bcCoreOptions && bcOptions?.bc_classes_variables?.bc_core_integration)) {	
		const styleTag = createDomElement('style', { id: 'bc_hover_preview'});		
		iframeDocument.body.children[0].insertBefore(styleTag, iframeDocument.getElementById('brx-content'));
		styleTagId = iframeDocument.getElementById('bc_hover_preview');		
	}
	
	innerPanel.addEventListener('mousedown', innerPanelEvents);
	if ( vueState.fullAccess && ((bcOptions?.bc_builder_tweaks?.bc_query_manager) || (bcOptions?.bc_classes_variables?.bc_core_integration)) ) {	
		leftPanelDragHandle.addEventListener('mousedown', innerPanelEvents);	
	}
    structurePanel.addEventListener('mousedown', structurePanelEvents);
	toolbar.addEventListener('mousedown', toolbarEvents);
	
	
	/********************************************************************************************************************************************/
	
	// helper to create dom element
	function createDomElement(tag, attributes = {}, children = []) {
		const element = document.createElement(tag);
		for (const [key, value] of Object.entries(attributes)) {
			if (key === 'classList') {
				value.forEach(cls => element.classList.add(cls));
			} else if (key === 'dataset') {
				Object.assign(element.dataset, value);
			} else if (key === 'textContent') {
			//	element.textContent = value;
				element.innerHTML = value.replace(/\n/g, '<br>');
			} else {
				element.setAttribute(key, value);
			}
		}
		if (typeof children === 'string') {
			element.innerHTML = children;
		} else {
			children.forEach(child => element.appendChild(child));
		}
		return element;
	}
	
	function createInputWrapper() {
		const inputWrapper = createDomElement('div', { classList: ['input'] }, [
			createDomElement('span', { classList: ['placeholder'], textContent: 'select one' }),
			createDomElement('span', { classList: ['bricks-svg-wrapper', 'clear-selection', 'hide'] }, `${clearSvg}`),
			createDomElement('span', { classList: ['bricks-svg-wrapper', 'arrow-down'] }, `${dropdownSvg}`),
			createDomElement('span', { classList: ['bricks-svg-wrapper', 'edit', 'hide'] }, `${editSvg}`),
			createDomElement('input', { id: 'query-name', classList: ['hide'] }),
		]);
		return inputWrapper;
	}
		
	function createOptionWrapper() {
		const optionsWrapper = createDomElement('div', { classList: ['options-wrapper'] }, [
			createDomElement('ul', { classList: ['dropdown'] })
		]);
		return optionsWrapper;
	}
	
	// function to close brickscodes modal on escape event
	function docEvents(event) {
		if (event.key === 'Escape' && event.isTrusted && vueState.fullAccess) {
			event.stopImmediatePropagation();
			closeModal();
		}
	}
	
	// query manager modal events
	function modalDivEvents(event) {
		const targetEle = event.target;	
		event.stopPropagation();	
		event.stopImmediatePropagation();

		if (event.type === 'mousedown' && (event.isTrusted || targetEle.id === 'bc-modal')) {
			if ( targetEle.id === 'bc-modal' ) {
				closeModal();
			}
			
			if ( vueState.activePanel === 'element' ) {
				const 	firstEleId 	= vueState[vueState.templateType] && vueState[vueState.templateType].length > 0 ? vueState[vueState.templateType][0]['id'] : null,
						lastEleId 	= vueState[vueState.templateType] && vueState[vueState.templateType].length > 0 ? vueState[vueState.templateType][vueState[vueState.templateType].length - 1]['id'] : null,
						currentId 	= vueState.activeId,
						currentQuerySettings = vueState.activeElement.settings.query ? vueState.activeElement.settings.query : {};

				const 	targetClosestPlaceholder 	= targetEle.closest('.placeholder'),
						targetClosestSelect			= targetEle.closest('div[type="select"]'),
						targetClosestTag			= targetEle.closest('.tags'),
						targetClosestTitle			= targetEle.closest('.title'),
						targetClosestLi				= targetEle.closest('li[data-index]'),
						targetClosestClear			= targetEle.closest('.clear-selection'),
						targetClosestDesc			= targetEle.closest('span.edit') && targetEle.closest('.query-desc'),
						targetClosestName			= targetEle.closest('span.edit') && targetClosestSelect,
						targetClosestQueryRecord	= targetEle.closest('.bc-query-search'),
						targetClosestSaveRecord		= targetEle.closest('.bc-query-save'),
						targetButtonSave			= targetEle.classList.contains('save'),
						targetButtonApply			= targetEle.classList.contains('apply'),
						targetButtonApplySave		= targetEle.classList.contains('apply-save'),
						targetButtonAbort			= targetEle.classList.contains('abort-update-query'),
						targetButtonUpdate			= targetEle.classList.contains('update-query'),
						targetClosestFooter			= targetEle.closest('.bc-modal-footer');
											
				if ( targetClosestPlaceholder ) {				
					if ( targetClosestQueryRecord ) {
						targetClosestSelect.classList.add('expand');
						if ( targetClosestTag ) {
							queryRecordModal.children[3].classList.remove('expand');
						} else if ( targetClosestTitle ) {
							queryRecordModal.children[1].classList.remove('expand');
						}
					} else if (targetClosestSaveRecord) {
						if ( targetClosestTag ) {					
							if ( querySaveModal.children[3].value.trim() === '' ) {
								querySaveModal.children[5].classList.add('expand');
							}
						}
					}
					
				} else if (targetClosestLi) {
					targetClosestSelect.classList.remove('expand');
					if ( targetClosestQueryRecord ) {
						if ( targetClosestTag ) {
							const selectedTag = targetClosestLi.getAttribute('data-tag');
							const queryTitleSelect = queryRecordModal.children[3].lastChild.lastChild;
							queryTitleSelect.innerHTML = '';
							queryRecordModal.children[1].children[0].children[0].textContent = selectedTag;
							queryRecordModal.children[1].children[0].children[0].classList.add('input-value');
							queryRecordModal.children[1].children[0].children[1].classList.remove('hide');
							queryRecordModal.children[3].children[0].children[0].textContent = 'select one';
							queryRecordModal.children[3].children[0].children[0].classList.remove('input-value');
							queryRecordModal.children[3].children[0].children[0].classList.remove('hide');
							queryRecordModal.children[3].children[0].children[1].classList.add('hide');
							queryRecordModal.children[3].children[0].children[3].classList.add('hide');
							queryRecordModal.children[3].children[0].children[4].classList.add('hide');
							queryRecordModal.children[5].children[0].value = '';
							queryRecordModal.children[5].children[1].classList.add('hide');	
							queryRecordModal.children[5].children[0].classList.add('disabled');							
							getTitlesByqTag(selectedTag, selectedTag, queryTitleSelect);
							modalFooter.children[1].setAttribute('disabled', 'disabled');
							modalFooter.children[2].setAttribute('disabled', 'disabled');
						} else if (targetClosestTitle) {
							const selectedTitle = targetClosestLi.children[0].textContent;
							const selectedQid	= targetClosestLi.getAttribute('data-query-id');
							if ( queryRecordModal.children[1].children[0].children[0].textContent === '' ) {
								const queryTagSelect = queryRecordModal.children[1].lastChild.lastChild;
								queryRecordModal.children[1].children[0].children[0].textContent = 'select one';
								queryRecordModal.children[1].children[0].children[0].classList.remove('input-value');
								queryRecordModal.children[1].children[0].children[1].classList.add('hide');
							}
							queryRecordModal.children[3].children[0].children[0].textContent = selectedTitle;
							queryRecordModal.children[3].children[0].children[0].classList.add('input-value');
							queryRecordModal.children[3].children[0].children[0].setAttribute('data-query-id', selectedQid);
							queryRecordModal.children[3].children[0].children[1].classList.remove('hide');
							if ( vueState.activeElement.settings.query && vueState.activeElement.settings._attributes && vueState.activeElement.settings._attributes.find(item => item.name === 'data-query-id' ) ) {
								queryRecordModal.children[3].children[0].children[3].classList.remove('hide');
								queryRecordModal.children[5].children[1].classList.remove('hide');
							}
							queryRecordModal.children[5].classList.add('input-value');
							queryRecordModal.children[5].children[0].value = targetClosestLi.getAttribute('data-query-desc');
							queryRecordModal.children[5].children[0].classList.add('disabled');		
							modalFooter.children[1].removeAttribute('disabled');
							queryArgsModal.classList.remove('hide');		
							querySettingsEditor.codemirror.setValue(JSON.stringify(queriesArgs[selectedQid]['query'], null, 4));
						}
					} else if (targetClosestSaveRecord) {
						if ( targetClosestTag ) {
							const selectedTag = targetClosestLi.getAttribute('data-tag');
							querySaveModal.children[5].children[0].children[0].textContent = selectedTag;
							querySaveModal.children[5].children[0].children[0].classList.add('input-value');
							querySaveModal.children[5].children[0].children[1].classList.remove('hide');
							querySaveModal.children[3].value = '';
							querySaveModal.children[3].classList.add('disabled');
						}
					}
					
				} else if (targetClosestClear) {
					targetClosestClear.classList.add('hide');
					if ( targetClosestQueryRecord ) {
						if (targetClosestTag) {
							queryRecordModal.children[1].children[0].children[0].textContent = 'select one';
							queryRecordModal.children[1].children[0].children[0].classList.remove('input-value');
							queryRecordModal.children[3].children[0].children[0].textContent = 'select one';
							queryRecordModal.children[3].children[0].children[0].classList.remove('input-value', 'hide');
							queryRecordModal.children[3].children[0].children[1].classList.add('hide');
							queryRecordModal.children[3].children[0].children[3].classList.add('hide');
							queryRecordModal.children[3].children[0].children[4].classList.add('hide');
							queryRecordModal.children[5].children[0].value = '';
							queryRecordModal.children[5].children[1].classList.add('hide');
							queryRecordModal.children[5].children[0].classList.add('disabled');		
							queryRecordModal.children[3].lastChild.lastChild.innerHTML = '';
							getTitlesByqTag('all', '', queryRecordModal.children[3].lastChild.lastChild);
							modalFooter.children[1].setAttribute('disabled', 'disabled');
							modalFooter.children[2].setAttribute('disabled', 'disabled');
						} else {
							queryRecordModal.children[3].children[0].children[0].textContent = 'select one';
							queryRecordModal.children[3].children[0].children[0].classList.remove('input-value');
							queryRecordModal.children[3].children[0].children[3].classList.add('hide');
							queryRecordModal.children[5].children[1].classList.add('hide');
							queryRecordModal.children[5].children[0].classList.add('disabled');	
							modalFooter.children[1].setAttribute('disabled', 'disabled');
							modalFooter.children[2].setAttribute('disabled', 'disabled');
							queryRecordModal.children[5].children[0].value = '';
						}
					} else if (targetClosestSaveRecord) {
						if (targetClosestTag) {
							querySaveModal.children[5].children[0].children[0].textContent = 'select one';
							querySaveModal.children[5].children[0].children[0].classList.remove('input-value');
							querySaveModal.children[3].classList.remove('disabled');
						}
					}
					
				} else if ( targetEle.classList.contains('close-settings') ) {
					queryArgsModal.classList.add('hide');
					
				} else if ( targetButtonSave && targetClosestFooter) {
					const eleQuerySettings = vueState.activeElement.settings.query && Object.keys(vueState.activeElement.settings.query).length > 0 ? vueState.activeElement.settings.query : null,
						  queryName		   = querySaveModal.children[1].value.trim() !== '' ? querySaveModal.children[1].value : `Element ${vueState.activeElement.name} (${vueState.activeElement.id}) - ${vueState.activeElement.settings.query.objectType} Query`,
						  queryTag		   = querySaveModal.children[3].value.trim() !== '' ? querySaveModal.children[3].value : querySaveModal.children[5].children[0].children[0].textContent,
						  queryDesc		   = querySaveModal.children[7].value.trim() !== '' ? querySaveModal.children[7].value : `Element ${vueState.activeElement.name} (${vueState.activeElement.id}) - ${vueState.activeElement.settings.query.objectType} Query At Template ${vueState.templateType} (${vueGlobal.bricks.postId}).`,
						  eleAttributes	   = vueState.activeElement.settings._attributes;
					let newQueryId;	  
					if ( eleQuerySettings ) {
						newQueryId = `qid_bc${vueGlobal.$_generateId()}`;
						let queryIdAttr = {
							'id': vueGlobal.$_generateId(), 
							'name': 'data-query-id',
							'value': newQueryId
						};
						let queryTagAttr = {
							'id': vueGlobal.$_generateId(), 
							'name': 'data-query-tag',
							'value': queryTag
						};
						if (eleAttributes && eleAttributes.length > 0) {
							if (eleAttributes && eleAttributes.find(item => item.name === 'data-query-id')) {
								eleAttributes.forEach((att) => {
									if ( att.name === 'data-query-id' ) {
										att.value = `qid_bc${vueGlobal.$_generateId()}`;
									}
									if ( att.name === 'data-query-tag' ) {
										att.value = queryTag;
									}
								});
							} else {
								eleAttributes.push(queryIdAttr);
								eleAttributes.push(queryTagAttr)
							}
						} else {
							vueState.activeElement.settings._attributes = [];
							vueState.activeElement.settings._attributes.push(queryIdAttr);
							vueState.activeElement.settings._attributes.push(queryTagAttr);
						}
					}
					ajaxUpdateQueryRecord(newQueryId, eleQuerySettings, queryName, queryDesc, queryTag, 'new');
					
				} else if (targetButtonApply && targetClosestFooter) {
					const eleAttributes = vueState.activeElement.settings._attributes;
					const selectedqId = queryRecordModal.children[3].children[0].children[0].getAttribute('data-query-id');
					const selectedQuery = selectedqId ? queriesArgs[selectedqId]['query'] : null;
					setCurrentPageQueryControl(selectedQuery, eleAttributes, selectedqId, currentId, firstEleId, lastEleId, 'apply');
					queryRecordModal.children[3].children[0].children[3].classList.remove('hide');
					queryRecordModal.children[5].children[1].classList.remove('hide');
					
				} else if (targetButtonApplySave && targetClosestFooter) {
					if ( (queryRecordModal.children[3].children[0].children[4].value.trim() === '' && (queryRecordModal.children[3].children[0].children[0].textContent === 'select-one' || queryRecordModal.children[3].children[0].children[0].textContent === '')) ||
						queryRecordModal.children[5].children[0].value.trim() === ''
					) {
						vueGlobal.$_showMessage('Please fill in query name and description');
					} else {
						bcModal.classList.add('confirm');
						bcModal.children[1].classList.remove('hide');
					}
					
				} else if (targetButtonUpdate) {
					const 	queryName 		= queryRecordModal.children[3].children[0].children[4].value.trim() !== '' ? queryRecordModal.children[3].children[0].children[4].value : queryRecordModal.children[3].children[0].children[0].textContent,
							queryDesc 		= queryRecordModal.children[5].children[0].value,
							querySettings 	= vueState.activeElement.settings.query && Object.keys(vueState.activeElement.settings.query).length > 0 ? vueState.activeElement.settings.query : null,
							selectedQueryId = queryRecordModal.children[3].children[0].children[0].getAttribute('data-query-id'),
							queryTag 		= queriesArgs[selectedQueryId]['qTag'],
							eleAttributes 	= vueState.activeElement.settings._attributes,
							contentEles		= vueState[vueState.templateType] && vueState[vueState.templateType].length > 0 ? vueState[vueState.templateType] : [];
							
					bcModal.classList.add('hide');	
					queryRecordModal.classList.add('hide');
					querySaveModal.classList.add('hide');
					queryArgsModal.classList.add('hide');
					bcModal.children[1].classList.add('hide');
					bcModal.classList.remove('confirm');					
					
					if (eleAttributes && eleAttributes.find(item => item.name === 'data-query-id') && selectedQueryId && querySettings) {
						if (contentEles) {
							for (let i = 0; i < contentEles.length; i++) {
								if (loopEles.includes(contentEles[i].name) && contentEles[i].id !== currentId && contentEles[i].settings._attributes) {
									contentEles[i].settings._attributes.forEach((item) => {
										if (item.value === selectedQueryId) {
											vueState.activeId = contentEles[i].id;
											setTimeout(() => {
												setCurrentPageQueryControl(querySettings, contentEles[i].settings._attributes, selectedQueryId, currentId, firstEleId, lastEleId, 'builder');
											}, 5);
										}
									});
								} else if ( contentEles[i].id === currentId ) {
									setCurrentPageQueryControl(querySettings, eleAttributes, selectedQueryId, currentId, firstEleId, lastEleId, 'apply');
								}
							}
						}
						ajaxUpdateQueryRecord(selectedQueryId, querySettings, queryName, queryDesc, queryTag, 'update');
					} else {
						vueGlobal.$_showMessage('Update failed.');
					}
				} 

				else if (targetButtonAbort) {
					bcModal.classList.remove('confirm');
					bcModal.children[1].classList.add('hide');
					
				} else if (targetClosestDesc) {
					queryRecordModal.children[5].children[0].classList.remove('disabled');
					modalFooter.children[2].removeAttribute('disabled');
					
				} else if ( targetClosestName ) {
					queryRecordModal.children[3].children[0].children[4].classList.remove('hide');
					queryRecordModal.children[3].children[0].children[4].value = queryRecordModal.children[3].children[0].children[0].textContent;
					queryRecordModal.children[3].children[0].children[0].classList.add('hide');
					modalFooter.children[2].removeAttribute('disabled');
					
				} else {
					queryRecordModal.children[1].classList.remove('expand');
					queryRecordModal.children[3].classList.remove('expand');
					querySaveModal.children[5].classList.remove('expand');
				}
				
			} 
			
			if ( ['element', 'theme-styles', 'settings-page'].includes(vueState.activePanel) ) {
				const 	targetMainAccordHeader		= targetEle.classList.contains('accordion-header') && targetEle.parentElement.classList.contains('accordion__item') && !targetEle.parentElement.classList.contains('accordion__sub_item'),
						targetSubAccordHeader		= targetEle.classList.contains('accordion-header') && targetEle.parentElement.classList.contains('accordion__sub_item'),
						targetDockLeft				= targetEle.closest('.bc-dock-left'),
						targetDockRight				= targetEle.closest('.bc-dock-right'),
						targetVariable				= targetEle.closest('li.bc-core-variable');
		
				if (targetMainAccordHeader) {
					const activeHeader = cfVariablesModal.children[0].querySelector('.active'),
						  activeHeaderParent = targetEle.parentElement;
						  
					targetEle.parentElement.classList.add('activate');	  
					if( activeHeader && !activeHeader.classList.contains('activate') ) {
						activeHeader.classList.remove('active');
						activeHeader.children[1].removeAttribute('style');
					}
					if ( activeHeaderParent.classList.contains('active') ) {
						activeHeaderParent.classList.remove('active');
						targetEle.nextElementSibling.removeAttribute('style');
					} else {
						targetEle.parentElement.classList.add('active');
						targetEle.nextElementSibling.style.display = 'block';
					}
					targetEle.parentElement.classList.remove('activate');	
					
				} else if (targetSubAccordHeader) {
					const activeSubHeader = cfVariablesModal.children[0].querySelector('.accordion__sub_item.active'),
						  activeSubHeaderParent = targetEle.parentElement;
					
					targetEle.parentElement.classList.add('activate');	
					if( activeSubHeader && !activeSubHeader.classList.contains('activate') ) {
						activeSubHeader.classList.remove('active');
						activeSubHeader.children[1].removeAttribute('style');
					}
					if ( activeSubHeaderParent.classList.contains('active') ) {
						activeSubHeaderParent.classList.remove('active');
						targetEle.nextElementSibling.removeAttribute('style');
					} else {
						targetEle.parentElement.classList.add('active');
						targetEle.nextElementSibling.style.display = 'block';
					}
					targetEle.parentElement.classList.remove('activate');	
					
				} else if (targetDockLeft) {
					const leftPanelWidth = vueState.panelWidth;
					bcModal.setAttribute('style', `left: 0; width: 100%;`);
					bcModal.children[0].setAttribute('style', `top: 40px; left: 0; width: ${leftPanelWidth}px; height: calc(100% - 40px)`);
					modalBody.setAttribute('style', `height: 100%;`);
					modalHeader.style.width = `${(vueState.panelWidth - 20 + 32)}px`;
					cfVariablesModal.setAttribute('style', `min-width: calc(${leftPanelWidth}px - 40px); max-width: calc(${leftPanelWidth}px - 40px); max-height: 95%; overflow-x: hidden; --accrd-item: 12px;`);
					cfVariablesModal.children[0].setAttribute('style', `width: calc(${leftPanelWidth}px - 40px);`);

				} else if (targetDockRight) {
					modalHeader.removeAttribute('style');
					const leftPanelWidth = vueState.panelWidth;
					bcModal.setAttribute('style',`--panel-width: ${leftPanelWidth}px`);
					bcModal.children[0].removeAttribute('style');
					modalBody.removeAttribute('style');
					cfVariablesModal.removeAttribute('style');
					cfVariablesModal.children[0].removeAttribute('style');
				} else if ( targetVariable && cssVarInput ) {
					cssVarInput.value = targetVariable.getAttribute('data-variables-name');
					cssVarInput.dispatchEvent(new Event('input'));
					cssVarInputValue = cssVarInput.value;
				}
			} 
		}
	}
	
	// helper reset brickscode modal on esc/click outside modal
	function closeModal() {
		const elementsToHide = [bcModal, modalHeader, modalFooter, queryRecordModal, querySaveModal, queryArgsModal];
		elementsToHide.forEach(el => el?.classList.add('hide'));
		
		bcModal.children[0].removeAttribute('style');
		modalBody.removeAttribute('style');
		
		if ( cfVariablesModal ) {
			cfVariablesModal.removeAttribute('style');
			cfVariablesModal.children[0].removeAttribute('style');
		}
		
		if ( modalHeader.hasAttribute('style') ) {
			modalHeader.removeAttribute('style');
		}

		if (modalBody.children[3]) {
			const child = modalBody.children[3];
			child.classList.add('hide');
			child.removeAttribute('style');
			const mainAccordHeader = child.querySelector('.accordion__item.active');
			if ( mainAccordHeader ) {
				mainAccordHeader.classList.remove('active');
				mainAccordHeader.children[1].removeAttribute('style');
			}
		}
		
		bcModal.style.setProperty('--panel-width', `${vueState.panelWidth}px`);
		bcModal.children[0].removeAttribute('style');
		modalBody.removeAttribute('style');		
	}
	
	// helper to get query name by query tags
	function getTitlesByqTag(qTag, selectedTag, parentDom) {
		Object.entries(queriesArgs).forEach(([key, value], index) => {			
			if ( qTag !== 'all' && value.qTag === selectedTag ) {
				const li = createDomElement('li', { 'data-index': index, 'data-query-id': key, 'data-query-tag': value.qTag, 'data-query-desc': value.qDesc}, [
					createDomElement('span', { textContent: value.qTitle }),
				]);
				parentDom.append(li);
			} 
			if ( qTag === 'all' ) {
				const li = createDomElement('li', { 'data-index': index, 'data-query-id': key, 'data-query-tag': value.qTag, 'data-query-desc': value.qDesc}, [
					createDomElement('span', { textContent: value.qTitle }),
				]);
				parentDom.append(li);
			}
		});
	}
	
	// ajax update query
	function ajaxUpdateQueryRecord(qId, qSettings, qName, qDesc, qTag, type) {
		let action, dataPayload;
		if ( type === 'update' ) {
			action = 'bc_update_query_settings';
		} else if ( type === 'new' ) {
			action = 'bc_add_new_query_settings';
		} else if ( type === 'all' ) {
			action = 'bc_refresh_query_settings';
		}
		
		if ( type === 'update' || type === 'new' ) {
			dataPayload = {
				action: action,
				nonce : vueGlobal.bricks.nonce,
				data: JSON.stringify(qSettings), 
				qId: qId,
				qName: qName,
				qDesc: qDesc,
				qTag: qTag,
				pid: vueGlobal.bricks.postId,
			}
		} else {
			dataPayload = {
				action: action,
				nonce : vueGlobal.bricks.nonce,
				pid: vueGlobal.bricks.postId,
			}
		}
		
		jQuery.ajax({
			type: 'POST',
			url: bc_builder_ajax.bc_builder_ajax_url,
			data: dataPayload,
			success: function(response) {
				if (response.success) {
					if ( type === 'update' || type === 'new' ) {
						queriesArgs = response.data.queriesArgs;							
						queriesTags = response.data.queriesTags;
						vueGlobal.$_showMessage(response.data.message);
					} else {
						queriesArgs = response.data.queriesArgs;							
						queriesTags = response.data.queriesTags;
						console.log(response.data.message);
					}
				} else {
					vueGlobal.$_showMessage(response.data.message);
				}
			},
			error: function(xhr, status, error) {
				// Handle error here
				console.error('AJAX Error:', status, error);
				vueGlobal.$_showMessage('An error occurred while processing the request.');
			}
		});			
	}
	
	// helper to update other elements at current page with the selected query id
	function setCurrentPageQueryControl(selectedQuery, eleAttributes, selectedqId, currentId, firstEleId, lastEleId, type) {
		if ( selectedQuery ) {
			if ( !eleAttributes ) {
				vueState.activeElement.settings._attributes = [
					{
						'id': vueGlobal.$_generateId(),
						'name': 'data-query-id',
						'value': selectedqId
					},
					{
						'id': vueGlobal.$_generateId(),
						'name': 'data-query-tag',
						'value': queriesArgs[selectedqId]['qTag']
					}
				];
			} else {
				for (const [index, item] of Object.entries(eleAttributes)) {
					if ( item.name === 'data-query-id' ) {
						item.value = selectedqId
					} else if ( item.name === 'data-query-tag' ) {
						item.value = queriesArgs[selectedqId]['qTag']
					}
				}
			}
			
			if (type === 'apply' || type === 'builder') {
				vueState.activeElement.settings.query = {};
				vueState.activeElement.settings.query = selectedQuery;
			}
			if (firstEleId && currentId !== firstEleId) {
				vueState.activeId = firstEleId;
			} else if (lastEleId && currentId !== lastEleId) {
				vueState.activeId = lastEleId;
			}
			setTimeout(() => {
				vueState.activeId = currentId;
			}, 5);
		}
	}
	
	// core framework modal hover events
	function modalVariableEvents(event) {
		const targetEle = event.target;	
		event.stopPropagation();
		event.stopImmediatePropagation();
		
		if ( event.type === 'mouseover' && event.isTrusted === true ) {
			if ( cssVarInput ) {
				cssVarInput.dispatchEvent(new Event('input'));
				cssVarInputValue = cssVarInput.value;
			}
			if ( cssVarInput && targetEle.closest('li.bc-core-variable') && targetEle.closest('li.bc-core-variable').matches(':hover') ) {	
				const targetVariable = targetEle.closest('li.bc-core-variable');
				if ( targetVariable ) {
					const variableName = targetVariable.getAttribute('data-variables-name');
					cssVarInput.value = variableName;					
					cssVarInput.dispatchEvent(new Event('input'));
				}
			} 
		} 
		
		if ( event.type === 'mouseout' && event.isTrusted === true && cssVarInput && targetEle.closest('li.bc-core-variable') ) {				
			cssVarInput.value = cssVarInputValue;
			cssVarInput.dispatchEvent(new Event('input'));
		}
		
		if ( event.type === 'mouseleave' && event.isTrusted === true && cssVarInput ) {		
			if (targetEle.classList.contains('bc-core-variables')) {			
				cssVarInput.value = cssVarInputValue;
				cssVarInput.dispatchEvent(new Event('input'));		
			}
		}
	}

	function innerPanelEvents(event) {		
		const targetEle = event.target;		
	
		if (event.type === 'mousedown' && event.isTrusted === true && targetEle.id !== 'bricks-panel-resizable') {		
			const 	activeElement	= vueState.activePanel === 'element' ? vueState.activeElement : null,
					activeSettings 	= vueState.activePanel === 'element' ? vueState.activeElement.settings : null,
					firstEleId 		= vueState.activePanel === 'element' && vueState[vueState.templateType] && vueState[vueState.templateType].length > 0 ? vueState[vueState.templateType][0]['id'] : null,
					lastEleId 		= vueState.activePanel === 'element' && vueState[vueState.templateType] && vueState[vueState.templateType].length > 0 ? vueState[vueState.templateType][vueState[vueState.templateType].length - 1]['id'] : null,
					iframeNode 		= vueState.activePanel === 'element' ? vueGlobal.$_getElementNode(vueState.activeId, 'iframe') : null;
console.log(vueState, vueGlobal);
			// bc modal auto expand 
			setTimeout(()=>{
				if(vueState.isPanelExpanded) {
					bcModal.setAttribute('style', `--panel-width: 600px;`);
				} else {
					bcModal.setAttribute('style', `--panel-width: ${vueState.panelWidth}px;`);
				}
			}, 200);

			// show/hide bc core variable modal
			if ( bcCoreOptions && vueState.fullAccess && targetEle.closest('.variable-picker-button') && bcOptions?.bc_classes_variables?.bc_core_integration ) {
				if ((querySaveModal && !querySaveModal.classList.contains('hide')) || (queryRecordModal && !queryRecordModal.classList.contains('hide'))) {
					closeModal();
				}
			
				const parentVarPickr = targetEle.closest('.variable-picker-button').parentElement;	
				if (parentVarPickr.getAttribute('data-control') !== 'code') {
					cssVarInput = targetEle.closest('.variable-picker-button').parentElement.querySelector('input');						
					if ( cssVarInput ) {
						cssVarInput.dispatchEvent(new Event('input'));
						cssVarInputValue = cssVarInput.value;
						const panelWidth = vueState.panelWidth;
						bcModal.classList.remove('hide');
						modalHeader.classList.remove('hide');
						bcModal.setAttribute('style', `--panel-width: ${panelWidth}px`);
						modalBody.children[3].classList.remove('hide');
					}
				}
			} else {
				cssVarInput = null;
			}
	
			// hover preview variable
			if (vueState.fullAccess && bcOptions?.bc_builder_tweaks?.bc_preview_class_variables) {
				if ( targetEle.closest('.variable-picker-button') ) {
					setTimeout(() => {
						const varPopup = targetEle.closest('.variable-picker-button').nextElementSibling;
						const parentVarPickr = targetEle.closest('.variable-picker-button').parentElement;
						if (varPopup && varPopup.classList.contains('expand')) {
							varPopup.children[0].lastChild.classList.add('css-variables');
							if (parentVarPickr.getAttribute('data-control') !== 'code') {
								cssVarInput = parentVarPickr.querySelector('input');						
								cssVarInputValue = cssVarInput ? cssVarInput.value : '';
							}
							varPopup.children[0].lastChild.removeEventListener('mouseover', hoverPreviewEvents);
							varPopup.children[0].lastChild.removeEventListener('mouseout', hoverPreviewEvents);
							varPopup.children[0].lastChild.removeEventListener('mouseleave', hoverPreviewEvents);
							varPopup.children[0].lastChild.addEventListener('mouseover', hoverPreviewEvents);
							varPopup.children[0].lastChild.addEventListener('mouseout', hoverPreviewEvents);
							varPopup.children[0].lastChild.addEventListener('mouseleave', hoverPreviewEvents);
						} else {
							cssVarInput = null;
							cssVarInputValue = null;
						}
					}, 250);
				} else if ( targetEle.closest('span.color-value-tooltip') && targetEle.closest('.bricks-control-preview') ) {
					setTimeout(() => {
						if ( targetEle.closest('.bricks-control-preview').nextElementSibling && targetEle.closest('.bricks-control-preview').nextElementSibling.classList.contains('bricks-control-popup') ) {
							cssVarInputValue = targetEle.closest('span.color-value-tooltip').hasAttribute('data-balloon') ? targetEle.closest('span.color-value-tooltip').getAttribute('data-balloon') : null;
							if ( targetEle.closest('.bricks-control-preview').nextElementSibling && targetEle.closest('.bricks-control-preview').nextElementSibling.classList.contains('bricks-control-popup') ) {
								const colorPaletteGrid = targetEle.closest('.bricks-control-preview').nextElementSibling.children[0];
								colorPaletteGrid.removeEventListener('mouseover', hoverPreviewEvents);
								colorPaletteGrid.removeEventListener('mouseout', hoverPreviewEvents);
								colorPaletteGrid.removeEventListener('mouseleave', hoverPreviewEvents);
								colorPaletteGrid.removeEventListener('mousedown', hoverPreviewEvents);
								colorPaletteGrid.addEventListener('mouseover', hoverPreviewEvents);
								colorPaletteGrid.addEventListener('mouseout', hoverPreviewEvents);
								colorPaletteGrid.addEventListener('mouseleave', hoverPreviewEvents);
								colorPaletteGrid.addEventListener('mousedown', hoverPreviewEvents);
								cssVarInput = targetEle.closest('.bricks-control-preview').nextElementSibling.children[2].children[0].children[0].children[0];
								cssVarInputValue = targetEle.closest('span.color-value-tooltip').hasAttribute('data-balloon') ? targetEle.closest('span.color-value-tooltip').getAttribute('data-balloon') : null;
							}
						}
					}, 250);
				}
			}
			
			// slider nested plus
			if ( bcOptions?.bc_native_bricks_elements_plus?.bc_slider_nested_plus && activeElement && activeElement.name === 'slider-nested' && vueState.fullAccess ) {				
				if (!activeSettings.optionsType || (activeSettings.optionsType && activeSettings.optionsType !== 'custom')) {
					let thumbId, thumbObj;
					
					if ( targetEle.id === 'thumbSliderId' && targetEle.tagName === 'INPUT' ) {
						thumbId = targetEle.value;				
					}
					
					const closestCheckboxControl = targetEle.closest('div[data-control="checkbox"]');
					const closestThumbNavControl = targetEle.closest('div[data-controlkey="thumbIsNavigation"]');
					const closestSyncControl	 = targetEle.closest('div[data-controlkey="enableSync"]');
					
					if ( closestCheckboxControl && closestThumbNavControl && !activeSettings.thumbIsNavigation ) {
						thumbId = activeSettings.thumbSliderId;
						thumbObj = vueGlobal.$_getElementObject(thumbId);
						if ( typeof thumbObj === 'object' && Object.keys(thumbObj).length > 0 ) {
							thumbObj.settings.isNavigation = true;
							vueGlobal.$_showMessage('options updated successfully.');
						} else {
							vueGlobal.$_showMessage('Invalid Thumb Slider Id, please try again');
						}
					} else if ( closestCheckboxControl && closestThumbNavControl && activeSettings.thumbIsNavigation ) {
						if ( !thumbId ) {
							thumbId = activeSettings.thumbSliderId;
						}
						thumbObj = vueGlobal.$_getElementObject(thumbId);
						if ( typeof thumbObj === 'object' && Object.keys(thumbObj).length > 0 ) {
							delete thumbObj.settings.isNavigation;
						}
					} else if ( closestCheckboxControl && closestSyncControl && activeSettings.enableSync ) {
						if (closestSyncControl) {
							delete activeSettings.thumbIsNavigation;
						}
					}
			
					if ( targetEle.closest('.control-group-title') && (targetEle.closest('li:not(.open)[data-control-group="pagination"]') || targetEle.closest('li:not(.open)[data-control-group="autoplayToggleGrp"]') || targetEle.closest('li:not(.open)[data-control-group="progressBarGrp"]')) ) {			
						if ( activeSettings.enableProgress && !iframeNode.querySelector('.splide__slide__progress') ) {
							const slideProgressBar = createDomElement('div', { classList: ['splide__progress', 'splide__slide__progress'] }, [
								createDomElement('div', { classList: ['splide__slide__progress__bar'] }),
							]);
							iframeNode.prepend(slideProgressBar);
						}
						if ( activeSettings.transitionProgress && !iframeNode.querySelector('.splide__transition__progress') ) {
							const transitionProgressBar = createDomElement('div', { classList: ['splide__progress', 'splide__transition__progress'] }, [
								createDomElement('div', { classList: ['splide__transition__progress__bar'] }),
							]);
							iframeNode.prepend(transitionProgressBar);
						}
						if ( activeSettings.fractionPagination && !iframeNode.querySelector('.splide__pagination__fraction') ) {						
							const fractionPag = createDomElement('div', { classList: ['splide__pagination__fraction'] }, [
								createDomElement('span', { classList: ['splide__pagination__current'], textContent: '1 / ' }),
								createDomElement('span', { classList: ['splide__pagination__total'], textContent: '3' }),
							]);
							iframeNode.prepend(fractionPag);
						}
						if ( activeSettings.playToggleButtons && !iframeNode.querySelector('.splide__toggle') ) {
							const playBtn = '<svg class="splide__toggle__play" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m22 12-20 11v-22l10 5.5z"></path></svg>';
							const pauseBtn = '<svg class="splide__toggle__pause" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m2 1v22h7v-22zm13 0v22h7v-22z"></path></svg>';
							const playPauseBtn = createDomElement('div', { classList: ['splide__toggle'], type: 'button' }, `${playBtn}${pauseBtn}`);
							iframeNode.prepend(playPauseBtn);
						}

					}
				}
			}

			// model viewer
			if ( bcOptions?.bc_custom_elements?.bc_model_viewer && activeElement && vueState.fullAccess ) {				
				if ( (targetEle.closest('li[data-index="0"]') || targetEle.closest('li[data-index="1"]')) && targetEle.closest('li[data-controlkey="gltfLoadingMode"]') ) {
					const modelViewer = vueGlobal.$_getElementNode(vueState.activeId, 'iframe');	
					const modelId = vueState.activeId;
					if (modelViewer) {
						const animationName = modelViewer.availableAnimations;						
						if ( animationName && animationName.length ) {
							setTimeout(() => {
								const optionsObj = animationName.reduce((acc, item) => {acc[item] = item; return acc;}, {});
								vueGlobal.bricks.elements['bc-model-viewer']['controls']['modelAnimationSelect']['options'] = optionsObj;
								if ( firstEleId && firstEleId !== modelId ) {
									vueState.activeId = firstEleId;
								} else if ( lastEleId && lastEleId !== modelId ) {
									vueState.activeId = lastEleId;
								}
							}, 350);	
							setTimeout(() => {
								vueState.activeId = modelId;
								vueState.activePanelGroup = 'modelOptions';
							}, 355);
						}
					}
				}
			}
			
			// query manager toggle button
			if ( bcOptions?.bc_builder_tweaks?.bc_query_manager && activeElement && vueState.fullAccess ) {				
				const closestPreview = targetEle.closest('.bricks-control-preview');
				if (closestPreview && closestPreview.parentElement && closestPreview.parentElement.getAttribute('data-control') === 'query') {
					setTimeout(() => {	
						if ( closestPreview.nextElementSibling && !document.getElementById('bc-query-manager') ) {
							const queryBtns = createDomElement('div', { id: 'bc-query-manager' }, [
								createDomElement('p', { classList: ['bc-save-info'], textContent: 'Query Manager ->' }),
								createDomElement('div', { id: 'bc-query-save', 'data-balloon': 'Save as New Query Record', 'data-balloon-pos': 'bottom-right' }, `${saveQuerySvg}`),
								createDomElement('div', { id: 'bc-query-select', 'data-balloon': 'Select From Query Records', 'data-balloon-pos': 'bottom-right' }, `${openQuerySvg}`),
							]);
							if (closestPreview.nextElementSibling) {
								closestPreview.nextElementSibling.prepend(queryBtns);
							}
							document.getElementById('bc-query-manager').removeEventListener('click', saveQueryVars);
							document.getElementById('bc-query-manager').addEventListener('click', saveQueryVars);
						}
					}, 280);	
				}
			}
			
			// slim left panel
			if ( bcOptions?.bc_builder_tweaks?.bc_slim_left_panel && vueState.activePanel === 'elements') {
				const targetParent = targetEle.closest('.collapse, .expand');
				const isCategoryToggle = targetParent && targetParent.parentElement.id === 'bc-category-toggle';

				if (isCategoryToggle) {
					const isCollapse = targetParent.classList.contains('collapse');
					const sibling = isCollapse ? targetParent.previousElementSibling : targetParent.nextElementSibling;
					const shouldExpand = isCollapse;

					sibling.classList.remove('hide');
					targetParent.classList.add('hide');

					const allCategories = elementCatPanel.querySelectorAll('#bricks-panel-elements-categories > li.category > .category-title');
					allCategories.forEach((cat) => {
						const condition = shouldExpand ? cat.classList.contains('expand') : !cat.classList.contains('expand');
						if (condition) {
							cat.click();
						}
					});
				}
			}
			
			// hover preview global class
			if ( vueState.fullAccess && activeElement && targetEle.closest('.active-class') &&
				((bcOptions?.bc_builder_tweaks?.bc_preview_class_variables) || 
				(bcCoreOptions && bcOptions?.bc_classes_variables?.bc_core_integration))
			) {
				setTimeout(() => {
					if (vueState.showElementClasses) {
						activeIframeNode = vueGlobal.$_getElementNode(vueState.activeId, 'iframe');	
						const cssClassesNode = innerPanel.querySelector('.css-classes');
						cssClassesNode.lastChild.classList.add('other-classes');
						cssClassesNode.lastChild.removeEventListener('mouseover', hoverPreviewEvents);
						cssClassesNode.lastChild.removeEventListener('mouseout', hoverPreviewEvents);
						cssClassesNode.lastChild.removeEventListener('mouseleave', hoverPreviewEvents);
						cssClassesNode.lastChild.addEventListener('mouseover', hoverPreviewEvents);
						cssClassesNode.lastChild.addEventListener('mouseout', hoverPreviewEvents);
						cssClassesNode.lastChild.addEventListener('mouseleave', hoverPreviewEvents);							
					}
				}, 300);
			}
						
			// copy to clipboard
			if ( vueState.fullAccess && activeElement && targetEle.id === 'copyClipboardElementId' && bcOptions?.bc_custom_elements?.bc_copy_clipboard ) {
				let changeEvent = false;
				if ( !changeEvent ) {
					targetEle.addEventListener("change", onChangeHandler);
					changeEvent = true;
				}			
			}
			
			// view form submission select form event
			if ( vueState.fullAccess && activeElement && bcOptions?.bc_custom_elements?.bc_form_submission && targetEle.closest('li[data-index]') && targetEle.closest('li[data-controlkey="queryFormId"]') ) {
				let eleIndex = 0;
				let firstEleId;
				const allEles = vueState[vueState['templateType']];
					
				if (allEles.length > 0) {	
					if ( allEles[eleIndex]['id'] === vueState.activeId ) {
						firstEleId = allEles[eleIndex + 1]['id'];
					} else {
						firstEleId = allEles[eleIndex]['id'];
					}
				}
				
				const closestSelectControl = targetEle.closest('div[data-control="select"]');
						
				const optionIndex = targetEle.closest('li[data-index]').getAttribute('data-index');
				const formOptionsObj = Object.keys(vueGlobal.bricks.elements['bc-form-submission']['controls']['queryFormId']['options']);				
				const currentId = vueState.activeId;
				const formId = formOptionsObj[optionIndex];
				if ( formId ) {
					ajaxUpdateFormField(formId, 'submission', currentId, firstEleId);
				}
			
				if ( closestSelectControl && closestSelectControl.previousElementSibling && closestSelectControl.previousElementSibling.getAttribute('for') === 'excludeFormField' && vueState.activeElement.settings.hasOwnProperty('queryFormId') && !Object.keys(vueGlobal.bricks.elements['bc-form-submission']['controls']['excludeFormField']['options']).length ) { 
					const formId = vueState.activeElement.settings.queryFormId;
					const currentId = vueState.activeId;
					if ( formId ) {
						ajaxUpdateFormField(formId, 'submission', currentId, firstEleId);
					}
				}	
			}
						
		} else if (event.type === 'mousedown' && event.isTrusted === true && targetEle.id === 'bricks-panel-resizable') {			
			panelIsResizing = true;
			document.addEventListener('mousemove', handleMouseMove);
			document.addEventListener('mouseup', handleMouseUp);
		}

	}
	
	// structure panel events
    function structurePanelEvents(event) {
        const targetEle = event.target;
	
        if (event.type === 'mousedown' && event.isTrusted === true) {			
			if ( targetEle.closest('li') && targetEle.closest('li').hasAttribute('data-element-name') && targetEle.closest('#bc-right-shortcut-bar') ) {
				const 	eleName 				= targetEle.closest('li').getAttribute('data-element-name'),
						shortcutBarElements 	= document.getElementById('bc-right-shortcut-bar').children;
			
				// show/hide shortcut bar buttons
				if ( eleName === 'action-bar' ) {
					if ( shortcutBarElements.length > 15 ) {
						for (let i = 0; i < shortcutBarElements.length; i++) {	
							if ( i < 15 ) {
								shortcutBarElements[i].classList.add('hide');
							} else {
								shortcutBarElements[i].classList.remove('hide');
							}
						
						}
					}
				} else if ( eleName === 'new-element-bar' ) {
					if ( shortcutBarElements.length > 15 ) {
						for (let i = 0; i < shortcutBarElements.length; i++) {
							if ( i >= 15 ) {
								shortcutBarElements[i].classList.add('hide');
							} else {
								shortcutBarElements[i].classList.remove('hide');
							}
						}
					}
				}
				
				// add new element and set active
				if ( !['action-bar', 'new-element-bar', 'hide-in-frontend', 'hide-in-canvas', 'toggle-children'].includes(eleName) ) {
					const 	addNewelement 	= vueGlobal.$_addNewElement({element:{name: eleName, settings:{}}}),
							addedEleId 		= vueState[vueState.templateType][vueState[vueState.templateType].length - 1]['id'];
					setTimeout(() => {		
						vueState.activeId 		= addedEleId;
						vueState.activePanel 	= 'element';
					}, 5);	
				}
				
			
				if ( eleName === 'hide-in-frontend' || eleName === 'hide-in-canvas' ) {
					targetEle.closest('li').classList.toggle('active');
					const 	clsName = eleName === 'hide-in-frontend' ? 'bc-hide-in-frontend' : (eleName === 'hide-in-canvas' ? 'bc-hide-in-canvas' : null),
							classId = getGlobalClassId(vueState.globalClasses, clsName),
							allEles = vueState[vueState.templateType];
						
					if (classId) {
						allEles.forEach((ele) => {
							const	hasClass = ele.settings._cssGlobalClasses,
									eleId = ele.id,
									structureItem = structurePanel.querySelector(`li#element-${eleId} > .structure-item`),
									hasActionBar = structureItem.querySelector(`ul.bc-actions`);
								
							if ( hasClass && hasClass.includes(classId) ) {
								if (targetEle.closest('li.active')) {					
									if ( !hasActionBar ) {
										const liHideFrontEle = createDomElement('li', { classList: ['bc-action', 'bc-hide-in-frontend', 'hide'], 'data-balloon': 'Hide in Frontend', 'data-balloon-pos': 'bottom-right', textContent: 'HIF' });
										const liHideCanvasEle = createDomElement('li', { classList: ['bc-action', 'bc-hide-in-canvas', 'hide'], 'data-balloon': 'Hide in Canvas', 'data-balloon-pos': 'bottom-right', textContent: 'HIC' });
										const actionsBar = createDomElement('ul', { classList: ['bc-actions']}, [liHideFrontEle, liHideCanvasEle]);
										structureItem.insertBefore(actionsBar, structureItem.children[structureItem.children.length - 1]);
										structureItem.querySelector(`ul.bc-actions > li.${clsName.replace(/_/g, '-')}`).classList.remove('hide');
									} else {
										structureItem.querySelector(`ul.bc-actions > li.${clsName.replace(/_/g, '-')}`).classList.remove('hide');
									}
								} else {									
									if ( hasActionBar ) {								
										structureItem.querySelector(`ul.bc-actions > li.${clsName.replace(/_/g, '-')}`).classList.add('hide');
									}
								}
							} else {
								if ( hasActionBar ) {								
									structureItem.querySelector(`ul.bc-actions > li.${clsName.replace(/_/g, '-')}`).classList.add('hide');
								}
							}	
						});
					}
				}
				
				// expand/collapse active element
				if ( eleName === 'toggle-children' ) {
					const 	targetClosestExpand 	= targetEle.closest('span[data-element-name="expand-all-childrens"]'),
							targetClosestCollapse 	= targetEle.closest('span[data-element-name="collapse-all-childrens"]'),
							currentActiveEle		= structurePanel.querySelector('.bricks-structure-list > li.active'),
							currentActiveId			= vueState.activeId;

					if ( targetClosestExpand ) {
						targetClosestExpand.classList.add('hide');
						targetClosestExpand.nextElementSibling.classList.remove('hide');
						recursivelyExpand(currentActiveEle);
						vueState.activeId = currentActiveId;
					} else if ( targetClosestCollapse ) {
						targetClosestCollapse.classList.add('hide');
						targetClosestCollapse.previousElementSibling.classList.remove('hide');
						recursivelyCollapse(currentActiveEle);
					}
				}
			}
        }		
    }
	
	// toolbar events
	function toolbarEvents(event) {
        const targetEle = event.target;	
		
        if (event.type === 'mousedown' && event.isTrusted === true) {	
			const 	darkToggle = targetEle.closest('span.dark'),
					lightToggle = targetEle.closest('span.light'),
					themeToggle = targetEle.closest('.core-theme-mode-toggle'),
					saveItem = targetEle.closest('li.save');
					
			if (darkToggle && themeToggle) {
				darkToggle.classList.add('hide');
				darkToggle.nextElementSibling.classList.remove('hide');
				iframeDocument.children[0].classList.add('cf-theme-dark');
				
			} else if (lightToggle && themeToggle) {
				lightToggle.classList.add('hide');
				lightToggle.previousElementSibling.classList.remove('hide');
				iframeDocument.children[0].classList.remove('cf-theme-dark');
				
			} else if (saveItem && vueState.fullAccess && bcOptions?.bc_builder_tweaks?.bc_query_manager) {			
				const changes = vueState.unsavedChanges.includes('content');
				const hasDeletedAction = vueState.history.some(item => item._action === "deleted");
				if ( changes && hasDeletedAction ) {
					const deletedItems = maybeUpdateQuery();

					if ( deletedItems.length > 0 ) {
						for (let i = 0; i < deletedItems.length; i++) {
							if (Object.keys(deletedItems[i].settings).length > 0) {							
								if (deletedItems[i].settings._attributes && Array.isArray(deletedItems[i].settings._attributes)) {	
									for (let j = 0; j < deletedItems[i].settings._attributes.length; j++) {	
										if ( deletedItems[i].settings._attributes[j].name === 'data-query-id' ) {
											ajaxUpdateQueryRecord('', '', '', '', '', 'all');	
											break;
										}
									};						
								}
							} 
						};
					}

				}
			} else {
				if ( !bcModal.classList.contains('hide') ) {
					const clickEvent = new Event('mousedown');
					bcModal.dispatchEvent(clickEvent);
				}
			}
		}
	}
	
	function hoverPreviewEvents(event) {		
		const targetEle = event.target;		
		event.stopImmediatePropagation();
		
		if ( event.type === 'mouseover' && event.isTrusted === true ) {			
			if ( targetEle.closest('li') && targetEle.closest('li').matches(':hover') && targetEle.closest('ul.other-classes') ) {
				if ( activeIframeNode ) {
					classHovered = targetEle.closest('li').children[0].textContent.slice(1);
					const classId = getGlobalClassId(vueState.globalClasses, classHovered);						
					let generatedCss = vueGlobal.$_generateCss('globalClass', classId, [vueState.activeElement.name]);
					styleTagId.textContent = generatedCss;	
					if ( classHovered !== 'bc-hide-in-canvas' ) {
						activeIframeNode.classList.add(classHovered);
					}
				}
				
			} else if ( targetEle.closest('li.variable-picker-item:not(.title)') && targetEle.closest('li.variable-picker-item:not(.title)').matches(':hover') && targetEle.closest('ul.dropdown.css-variables') ) {
				const varName = targetEle.closest('li.variable-picker-item').children[0].textContent;
				const varString = `var(--${varName})`;
				
				if ( cssVarInput ) {
					cssVarInput.value = varString;
					cssVarInput.dispatchEvent(new Event('input'));
				}
				
			} else if ( targetEle.closest('li.color') && targetEle.closest('li.color').matches(':hover') && targetEle.closest('ul.color-palette.grid') ) {
				const color = targetEle.closest('li.color').children[0].getAttribute('data-balloon');
				if ( color.includes('#') ) {
					const hexColor = color.match(/#[0-9a-fA-F]{6}\b/);
					cssVarInput = targetEle.closest('.bricks-control-popup').querySelector('.color-inputs > .hex input');
					if (cssVarInput) {
						cssVarInput.value = hexColor;
						cssVarInput.dispatchEvent(new Event('input'));
					}
				} else if ( color.includes('var(') ) {
					cssVarInput = targetEle.closest('.bricks-control-popup').querySelector('.color-inputs > .raw input');
					cssVarInput.value = color;
					cssVarInput.dispatchEvent(new Event('input'));
				}
			}				
		} 
		
		if ( event.type === 'mouseout' && event.isTrusted === true ) {	
			if ( targetEle.closest('li') && !targetEle.closest('li').matches(':hover') && targetEle.closest('ul.other-classes') ) {
				if ( activeIframeNode ) {
					activeIframeNode.classList.remove(classHovered);
					styleTagId.textContent = '';	
				}
			} else if ( targetEle.closest('li.variable-picker-item:not(.title)') && !targetEle.closest('li.variable-picker-item:not(.title)').matches(':hover') && targetEle.closest('ul.dropdown.css-variables') ) {
				if ( cssVarInput && cssVarInputValue ) {
					cssVarInput.value = cssVarInputValue;
					cssVarInput.dispatchEvent(new Event('input'));
				}
			} else if ( targetEle.closest('li.color') && !targetEle.closest('li.color').matches(':hover') && targetEle.closest('ul.color-palette.grid') ) {
				if ( cssVarInput && cssVarInputValue ) {
					cssVarInput.value = cssVarInputValue;
					cssVarInput.dispatchEvent(new Event('input'));
				}
			}
			
		} 
		
		if ( event.type === 'mouseleave' && event.isTrusted === true ) {		
			if (targetEle.classList.contains('css-variables') || targetEle.classList.contains('color-palette')) {
				if ( cssVarInput ) {					
					cssVarInput.value = cssVarInputValue;
					cssVarInput.dispatchEvent(new Event('input'));			
				}
			}			
			setTimeout(() => {
				if (targetEle.classList.contains('other-classes') && targetEle.closest('.css-classes')) {
					if ( activeIframeNode ) {
						activeIframeNode.classList.remove(classHovered);
						styleTagId.textContent = '';						
					}
				}
			}, 50);
		}
		
		if ( event.type === 'mousedown' && event.isTrusted === true ) {	
			if ( targetEle.closest('li.color') && targetEle.closest('ul.color-palette.grid') ) {
				const color = targetEle.closest('li.color').children[0].getAttribute('data-balloon');
				if ( color.includes('#') ) {
					cssVarInputValue = color.match(/#[0-9a-fA-F]{6}\b/);
				} else if ( color.includes('var(') ) {
					cssVarInputValue = color;
				}
			}
		}
	}
	
	// helper to get global class id by global class name
    function getGlobalClassId(array, nameToSearch) {
        const foundObject = array.find(item => item.name === nameToSearch);
        if (foundObject) {
            return foundObject.id;
        } else {
            return null;
        }
    }
	
	// buttons events for open query manager modal 
	function saveQueryVars(event) {
		const targetEle = event.target;
		event.stopImmediatePropagation();
		const 	panelWidth 			= vueState.panelWidth;
				attrObj 			= vueState.activeElement.settings._attributes;
				
		bcModal.style.setProperty('--panel-width', `${panelWidth}px`);

		if ( targetEle.closest('#bc-query-save') ) {
			if ( !modalHeader.classList.contains('hide') ) {
				closeModal();
			}
			const 	queryTitlePlaceholder 	= querySaveModal.children[1],
					queryTagPlaceholder 	= querySaveModal.children[5].children[0].children[0];

			bcModal.classList.remove('hide');
			modalBody.children[2].classList.add('hide');
			queryRecordModal.classList.add('hide');
			querySaveModal.classList.remove('hide');
			querySaveModal.children[1].value = '';
			querySaveModal.children[3].value = '';
			querySaveModal.children[7].value = '';
			querySaveModal.children[3].classList.remove('disabled');
			querySaveModal.children[4].classList.remove('hide');
			querySaveModal.children[5].classList.remove('expand', 'hide');
			querySaveModal.children[5].children[0].children[0].classList.remove('input-value');
			querySaveModal.children[5].children[0].children[1].classList.add('hide');
			modalFooter.classList.remove('hide');
			modalFooter.children[0].classList.remove('hide');
			modalFooter.children[1].classList.add('hide');
			modalFooter.children[2].classList.add('hide');
			
			const categorySelect = querySaveModal.children[5].lastChild.lastChild;
			categorySelect.innerHTML = '';	
			if (queriesTags && queriesTags.length > 0) {
				queryTagPlaceholder.textContent = 'select one';
				queriesTags.forEach((tag, index) => {
					const li = createDomElement('li', { 'data-index': index, 'data-tag': tag }, [
						createDomElement('span', { textContent: tag }),
					]);
					categorySelect.append(li);
				});
			} else {
				queryTagPlaceholder.textContent = 'No query record found';
			}
			
		} else if ( targetEle.closest('#bc-query-select') ) {
			if ( !modalHeader.classList.contains('hide') ) {
				closeModal();
			}
			const 	queryTitlePlaceholder 	= queryRecordModal.children[3].children[0].children[0],
					queryTagPlaceholder 	= queryRecordModal.children[1].children[0].children[0],
					queryClearBtn 			= queryRecordModal.children[3].children[0].children[1],
					eleAttributes			= vueState.activeElement.settings._attributes;
			
			bcModal.classList.remove('hide', 'confirm');
			bcModal.children[1].classList.add('hide');
			modalBody.children[2].classList.add('hide');
			querySaveModal.classList.add('hide');
			queryRecordModal.classList.remove('hide');
			queryRecordModal.children[1].classList.remove('expand');
			queryRecordModal.children[1].children[0].children[1].classList.add('hide');
			queryRecordModal.children[3].classList.remove('expand');
			queryRecordModal.children[3].children[0].children[0].classList.remove('hide');
			queryRecordModal.children[3].children[0].children[3].classList.add('hide');
			queryRecordModal.children[3].children[0].children[4].classList.add('hide');
			queryRecordModal.children[5].children[0].value = '';
			queryRecordModal.children[5].children[0].classList.add('disabled');
			queryRecordModal.children[5].children[1].classList.add('hide');			
			queryTagPlaceholder.classList.remove('input-value');
			queryTitlePlaceholder.classList.remove('input-value');
			queryClearBtn.classList.add('hide');
			modalFooter.classList.remove('hide');
			modalFooter.children[0].classList.add('hide');
			modalFooter.children[1].setAttribute('disabled', 'disabled');
			modalFooter.children[2].setAttribute('disabled', 'disabled');
			modalFooter.children[1].classList.remove('hide');
			modalFooter.children[2].classList.remove('hide');
			
			const categorySelect = queryRecordModal.children[1].lastChild.lastChild;
			categorySelect.innerHTML = '';	
			if (queriesTags && queriesTags.length > 0) {
				queryTagPlaceholder.textContent = 'select one';
				queriesTags.forEach((tag, index) => {
					const li = createDomElement('li', { 'data-index': index, 'data-tag': tag }, [
						createDomElement('span', { textContent: tag }),
					]);
					categorySelect.append(li);
				});
			} else {
				queryTagPlaceholder.textContent = 'No query record found';
			}
			
			const allTitlesSelect = queryRecordModal.children[3].lastChild.lastChild;			
			allTitlesSelect.innerHTML = '';	
			if ( queriesArgs && Object.keys(queriesArgs).length > 0 ) {
				queryTitlePlaceholder.textContent = 'select one';
				getTitlesByqTag('all', '', allTitlesSelect);
				if (eleAttributes && eleAttributes.length > 0) {
					eleAttributes.forEach((item) => {
						if ( item.name === 'data-query-id' ) {
							queryRecordModal.children[3].children[0].children[0].textContent = queriesArgs[item.value]['qTitle'];
							queryRecordModal.children[3].children[0].children[0].classList.add('input-value');
							queryRecordModal.children[3].children[0].children[0].setAttribute('data-query-id', item.value);
							queryRecordModal.children[3].children[0].children[1].classList.remove('hide');
							queryRecordModal.children[3].children[0].children[3].classList.remove('hide');
							queryRecordModal.children[5].classList.add('input-value');
							queryRecordModal.children[5].children[0].value = queriesArgs[item.value]['qDesc'];
							queryRecordModal.children[5].children[1].classList.remove('hide');
							queryRecordModal.children[5].children[0].classList.add('disabled');	
							queryArgsModal.classList.remove('hide');		
							querySettingsEditor.codemirror.setValue(JSON.stringify(queriesArgs[item.value]['query'], null, 4));
						}
					})
				}
			} else {
				queryTitlePlaceholder.textContent = 'No query record found';
			}
		}
	}
	
	// copy to clipboard function
	function onChangeHandler(event) {
		let eleObj = ''
		async function readFromClipboard() {
			try {
				eleObj = await navigator.clipboard.readText();
			} catch (error) {
				eleObj = false;
				vueGlobal.$_showMessage(`Extract Element Json failed.`);
				console.error("Failed to read clipboard contents:", error);
			}
		}

		const 	currentId 			= vueState.activeId,
				eleId 				= event.target.value,
				templateTypeEles 	= vueState[vueState.templateType];
		event.target.removeEventListener("change", onChangeHandler);
		changeEvent = false;
		
		if ( templateTypeEles.some(item => item.id === eleId) ) {
			vueState.activeId = eleId;
			vueGlobal.$_setActiveElement();
			vueState.activePanel = 'element';
			setTimeout(() => {
				vueGlobal.$_copyElements();
				readFromClipboard();
				vueState.activeId = currentId;	
				vueState.activePanel = 'element';
			}, 5);	
			setTimeout(() => {
				const eleJsonTextarea = document.querySelector('li[data-controlkey="copyClipboardElementJson"] textarea');
				if ( eleObj !== false && eleJsonTextarea ) {
					let parseEleObj = JSON.parse(eleObj);
					parseEleObj.sourceUrl = "";			
					eleJsonTextarea.value = JSON.stringify(parseEleObj);
					eleJsonTextarea.dispatchEvent(new Event('input'));
				} else {
					eleJsonTextarea.value = '';
					eleJsonTextarea.dispatchEvent(new Event('input'));
				}
			}, 300);
		} else {
			const eleJsonTextarea = document.querySelector('li[data-controlkey="copyClipboardElementJson"] textarea');
			if ( eleJsonTextarea ) {
				eleJsonTextarea.value = '';
				eleJsonTextarea.dispatchEvent(new Event('input'));
				vueGlobal.$_showMessage(`No Element found with Element Id ${eleId}.`);
			}
		}
	}
	
	// left panel drag events
	function updateResize(e) {
        if (panelIsResizing && bcModal) {
			bcModal.setAttribute('style', `--panel-width: ${mainPanel.offsetWidth}px`);
        }
    }
            
    function handleMouseMove(e) {
        if (panelIsResizing) {			
            updateResize(e);
        }
    }
	
	function handleMouseUp() {
        panelIsResizing = false;
        // remove drag listener
        document.removeEventListener('mousemove', handleMouseMove);
        document.removeEventListener('mouseup', handleMouseUp);
    }
	
	// helper to expand all children
	function recursivelyExpand(element) {
		if (element && element.children && element.children.length > 0) {
			const clickEvent = new Event('click');
			if ( element.children[0].children[0].getAttribute('data-name') === 'arrow-right' ) {
				element.children[0].children[0].dispatchEvent(clickEvent);
			}

			if (element.children[1] && element.children[1].children) {
				Array.from(element.children[1].children).forEach((child) => {
					if (child.children && child.children.length > 0) {
						recursivelyExpand(child);
					}
				});
			}
		}
	}
	
	// helper to collapse all children
	function recursivelyCollapse(element) {
		if (element && element.children && element.children.length > 0) {
			const clickEvent = new Event('click', { bubbles: false }); // Prevent bubbling		
			if (element.children[1] && element.children[1].children) {
				const subChildren = Array.from(element.children[1].children).reverse();

				subChildren.forEach((child) => {
					if (child.children && child.children.length > 0) {
						recursivelyCollapse(child);
					}
				});
			}	
			if (element.children[1] && element.children[1].children.length > 0) {
				if ( element.children[0].children[0].getAttribute('data-name') === 'arrow-down' ) {
					element.children[0].children[0].dispatchEvent(clickEvent);
				}
			}
		}
	}
	
	// helper to check if any query element deleted, if yes next page load update query records
	function maybeUpdateQuery() {
		let deletedItems = vueState.history
			.filter(item => item._action === "deleted")  // Only deleted actions
			.map(item => {
				let result = [];
				for (let key in item.content) {
					let contentItem = item.content[key];
					
					// If content is an object with name array (case like {"id": ["..."], "name": ["..."], "settings": {...}})
					if (Array.isArray(contentItem) && contentItem.id && Array.isArray(contentItem.id)) {
						contentItem.id.forEach((id, index) => {
							let name = contentItem.name[index];
							if (loopEles.includes(name)) {
								result.push({
									id: id,
									name: name,
									settings: contentItem.settings || {}
								});
							}
						});
					}

					// If content is an array of objects (case like [{ "id": "...", "name": "..." }])
					if (Array.isArray(contentItem)) {
						contentItem.forEach(obj => {
							if (obj.name && loopEles.includes(obj.name)) {
								result.push({
									id: obj.id,
									name: obj.name,
									settings: obj.settings || {}
								});
							}
						});
					}
				}
				return result;
			})
			.flat();  // Flatten the resulting array

		// Remove duplicates based on id
		let uniqueDeletedItems = deletedItems.filter((value, index, self) => 
			index === self.findIndex((t) => (
				t.id === value.id
			))
		);
		return uniqueDeletedItems;
	}
	
	function ajaxUpdateFormField(idOrKey, actionType, formId, rootEleId) {
		let action = '';
		if (actionType === 'submission') {
			action = 'bc_get_submission_form_fields'; 
		}
			
		jQuery.ajax({
			type: "POST",
			url: bc_builder_ajax.bc_builder_ajax_url,
			data: {
				action: action,
				nonce : vueGlobal.bricks.nonce,
				groupKey : idOrKey,
			},
			success: function(response) {
				if (response.success) {	
					if ( actionType === 'submission' ) {
						vueGlobal.bricks.elements['bc-form-submission']['controls']['excludeFormField']['options'] = response.data.fields;
						vueState.activeId = rootEleId;
						setTimeout(() => {
							vueState.activeId = formId;
							vueState.activePanel = 'element';
						}, );
					}
					
				}
			}
		})
	}
}