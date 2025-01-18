# Brickscodes
Elevate your Bricks Builder experience with this powerful plugin designed to seamlessly integrate with Bricks. Packed with custom elements, editor enhancements, custom conditions, custom dynamic tags, native elements enhancements, etc. This plugin extends the functionality of Bricks while adhering to WordPress best practices.

## Installation
* Download brickscodes.zip from lateset Releases.
* From your WordPress dashboard, choose Plugins > Add New.
* Click Upload Plugin at the top of the page.
* Click Choose File, locate the plugin .zip file, then click Install Now.
* After the installation is complete, click Activate Plugin.
* Any new update release, you will get the same automatic update notice from your plugin dashboard.

## Builder Tweaks
###### Compact Left Panel
* Expand/Collapse element category groups.
* Compact elements list.
###### Preview Global Class/Variables/Color Palette gird on hover 
###### Custom Builder Saved Message
###### Disable Elements by Template Type (Header/Footer/Content) (for Non-Administrator)
* When you click the 'Edit with Bricks' link or button on a Post, Page, or Template, disabled elements will be automatically applied based on the template type.
* Note: If disabled elements are used in the current Template/Content, the Builder will display a warning in the console with the error: generateCss: controls for element not found. The elements and their controls will not be rendered in the Builder. However, everything will continue to function normally in both the Builder and the Frontend.
###### Builder Preloader Background Color
###### Favorite Remote Templates
* A favorite button is available on all remote templates.
* All templates marked as favorites will be added to your custom favorite remote template library.
* Remove favorite templates using either the favorite or remove button.
###### Query Manager (https://bricksbuilder.io/ideas/#12214)
* First load automatically pulling all query settings from element across entire site and save as first query records in query manager modal.
* Save new query record - Set your query settings on element as usual, once finalized, click save, give a query name/tag/description and save.
* Apply existing query records - Open query manager modal, select from the record, click apply, query settings will populate to the element query control.
* Edit exisiting query records - Open query manager modal, modal will display current applied query record, change your element query settings as normal, once finalized, click edit, give a new name and description or make no changes, click Apply and Update. Query record with the same edited query id will get updated across entire site.
* If plugin deactivated or feature disabled, your elements query settings will continue to work. Saved query records will be reset.
###### Add New Elements Shortcut Bar
* New shortcut bar at Structure Panel to quickly add predefined common elements and auto set active to the newly added element.
###### Element Visibility in Builder/Frontend (https://bricksbuilder.io/ideas/#8663)
* Apply Global Class 'bc-hide-in-canvas' to element, element will render in Frontend but hide in Builder. 
* Apply Global Class 'bc-hide-in-frontend' to element, element will not render in Frontend, element with red border render in Canvas.
* New action shortcut bar at Structure Panel to quickly toggle to check elements in 'bc-hide-in-canvas' and 'bc-hide-in-frontend.
###### Expand/Collapse All Children of Active Element
* New action shortcut bar at Structure Panel to quickly toggle to expand/collapse current active element at Structure Panel.
###### Disable Icon Library Selection
* When this feature is enable, it will automatically disable icon libraries selection except SVG in Builder.
* All Elements default icon set by Bricks will be automatically unset.
* Addtionally Controls added in Bricks -> Settings -> Page Settings. You can enable it on per page basis to dequeue icon libraries and unset icons other than SVG in frontend. Disable it will revert back to normal. 
## Conditions
###### New Template Condition Control (https://bricksbuilder.io/ideas/#5978)
* New control allow you to use dynamic data from custom field, compare the value you defined and add the score 10 to template.
## Interactions
###### New Keyboard Escape Event Interactions
* New Trigger control for keyboard escape event. Works with all actions.
## Dynamic Tags
###### User Ip Address
* {bc_user_ip}
###### User Last Logged-in
* {bc_user_last_login} - Display Date.
* {bc_user_last_login:30} - Display human_time_diff format if less than the argument value.
###### User Registered Date
* {bc_user_register_date}
###### Post Terms
* {bc_post_type_taxonomy_terms:post_type_slug:taxonomy_slug} - This will return terms id and name for select/checkbox/radio options. Replace post_type_slug and taxonomy_slug to your target slug.
## Global Classes and Variables
###### Core Framework Free Integration (reference: https://docs.coreframework.com/development/coreframework-helper )
* Auto sync changes that made on Core Framework settings page to Builder.
* All Core Framework classes and variable are auto import to Builder.
* Separate modal for Core Framework Variables.
* When deactivate plugin, you may choose to keep Core Framework.
* Core Framework dark/light mode toggle in Builder.
###### Custom Framework Integration
* Upload any stylesheet, let Brickscodes automatically extract and import Variables in to Builder.
## Custom Elements
###### 3D Model Viewer
* Support glb/gltf, model animation, lazy load, camera control, etc. More info here https://modelviewer.dev/docs/
###### Image Before After Viewer
* Convert the library from https://image-compare-viewer.netlify.app/ into Bricks Builder element.
###### Image Hotspots (from Codepen https://codepen.io/abcretrograde/pen/dKGOEL) (https://bricksbuilder.io/ideas/#4958)
* Hotspot Content
  * Support static and query loop content.
  * Option to always show title.
  * Click or Click/Hover reveal content.
  * Auto positioning Content direction.
* Hotspot Marker
  * All markers must be defined here, whether the marker content is static or generated from a Query Loop, avoid the need to switch back and forth between the Builder and post meta to fine-tune positions for different breakpoints.
  * Responsive marker positioning is supported, allowing adjustments directly within the Builder.
* Hotspot Image
  * Source from media/url/dynamic data
  * Image srcset suuport.
###### Lottie Player
* Support the new DotLottie format (.lottie) alongside traditional .json files. Files can be added via media uploads, dynamic data sources, or custom URLs. Leverage the DotLottieWorker for optimized animation rendering. Provide support for configurable options such as speed, mode, autoplay, loop, and start-end frames. More information here https://developers.lottiefiles.com/docs/dotlottie-player/dotlottie-web/
###### Copy to Clipboard Button (https://bricksbuilder.io/ideas/#8270)
* Support Dynamic tag as copy content.
* Specify class/id to copy its text content, option to specfiy tags to exlude.
* Specify element bricks id and automatically pull element json as copy content. (require user log in to copy)
* Select Template to export in frontend. (require user log in to export)
###### View Form Subimmission in Frontend
* Select User Roles allow to view.
* option to excludes form fields.
* Limit total entries return.
* Support filter by date.
* Support pagination for default table.
* Supports advanced tables with features like export, pagination, column reordering, and responsiveness. Assets are loaded only when these features are enabled. (https://datatables.net/manual/index)
## Enhance Native Bricks Elements
###### Layout Elements (Section, Container, Block, Div)
* Animate SVG Shape Divider on scroll:
  * Extra control added to enable shape divider animation on layout elements.
  * Prepare your svg shape divider as stated in this article https://tympanus.net/codrops/2022/06/08/how-to-animate-svg-shapes-on-scroll/
  * Simply enable it, and it will work seamlessly. GSAP, ScrollTrigger, and Lenis assets are loaded on the frontend only when the feature is enabled.
###### Slider Nested
* Support Splide Extension UrlHashNavigation, AutoScroll, Intersection. Extension assets only load if enabled.
* Support sync sliders.
* Number/fraction pagination.
* Progress Bar for overall progress and slide duration progress.
* Play/Pause button for autoplay and autoscroll.
* Disable Slider at selected breakpoint.
###### Tabs Nested
* Enhance Tabs Nested with Controls such as Tab Menu Horizontol Scroll, Vertical Tab Menu, Convert to Accordion by selected breakpoint. (https://bricksbuilder.io/ideas/#8314)
* The rest can be handle by Bricks Interactions for content animation with target .brx-open
###### Back to Top
* Add scroll progress to Back to Top Element.
* When enabled, adding a Back to Top element will automatically include a scroll progress indicator. Customize its style as desired.
###### Form
* Icon Radio/Checkbox (https://bricksbuilder.io/ideas/#13326)
* Extra HTML5 pattern attribute for Password and Email field type. Validate in frontend with pure HTML5 validation and backend with form validation hook.
* International telephone support seamlessly integrates with the Bricks form's Tel field type. Automatically detects the user's country using multiple free lookup services, ensuring reliability even if some services fail. Provides real-time telephone number validation and saves the number in full, including the country code. (https://github.com/jackocnr/intl-tel-input)
* Password visibility toggle integrate to Bricks form's password field. (depreciated and removed)
* Signature Pad with File Preview. Seamlessly integrates with the Bricks Form element. Support viewport resize and redraw signed signature. Use form field type 'text' as Signature Pad input. Once enabled, it will automatically create field type 'file' for preview and upload. Assets only loaded if enabled. (https://github.com/szimek/signature_pad)
* File uploads thumbnail preview. Auto filter out duplication. Seemless integrate with Bricks Form File type.
* Offline Brickscodes Remote Templates are accessible only if you activate the Form Abandonment or Confirmation Popup Modal Before Submission module. Save the sample popup template as a popup template type and style it according to your preferences. However, it is essential not to alter the JavaScript event interactions for the popup button.
* Supports ACF repeater fields, allowing you to add, delete, and sort repeater rows while enforcing the maximum row limit based on the repeater's settings.

* Datepicker Extra Options
  * Options to disable weekend.
  * Options 'single, multiple, range' selection mode.
  * options to show multiple months, fallback to single month if viewport size less than 600px.
  * Options current time as default hour and minute.
  * Options to select day as first day of the week.
  * Options to disable calendar.
  * Options to set start and end dates for dates selection.
  * Options to disable multiple/range of dates.
  * Options to automatically disable dates based on selected dates from another Datepicker.

* HTML Email Template Builder (Module) (https://docs.unlayer.com/docs/getting-started)
  * Html Email Template Builder powered by Unlayer in Bricks Builder.
  * Email Builder only can open when you working on form.
  * Save Email Template locally.
  * Load saved template locally.
  * Support Bricks form dynamic fields.(https://academy.bricksbuilder.io/article/form-element/)
  * Export the HTML email template with a single click to Form Admin, Confirm Email Content, or both.
  * Button to open Media Library, select image as needed. Image Url will be copy to clipboard. You can paste into Unlayer Image Block Url input.

* Advance User Registration (Module)
  * Seamlessly integrates with Bricks' Form default User Registration action.
  * Offers an option to validate user email addresses using a secure, time-based activation link before creating the user account. (https://bricksbuilder.io/ideas/#13319)
    * When enabled, all form fields except the email field are removed (not rendered) on the frontend.
    * Validates the user's email address before sending the activation link.
    * Once the user receives the activation link and clicks it, they are redirected back to the form page. The link and email address are validated. If validation is successful, all form fields become available, with the email address pre-filled.
    * The user can then continue the registration process.
  * Supports ACF fields:
    * Create an ACF User field group and assign its location to the user form.
    * Add form fields as usual, including fields for your custom ACF User fields.
    * Choose the default Bricks User Registration form action.
    * A new set of controls in the Registration group allows you to select an ACF User field group.
    * Once the ACF User field group is selected, the corresponding custom fields are automatically populated in the mapping repeater fields. Simply map the ACF fields to the form fields as needed.
    * Upon submission, all ACF User custom fields will be automatically populated and saved to the user profile. Manual publishing or saving of the user post is not required.

* Frontend Form Update User Metas (Module)
  * The form is displayed only when the user is logged in. By default, fields for email, username, and password are disabled and cannot be modified. However, there is an option to enable password changes.
  * Add form fields as needed, including those for custom ACF User fields. Populate field values with dynamic wp user and ACF user field data to pre-fill the form with user information.
  * Supported ACF fields:
    * Create an ACF User field group and assign its location to the user form.
    * Add form fields as required, including custom ACF User fields.
    * Select the custom form action update-user-metas.
    * In the User Profile/Metas group, use the provided controls to choose an ACF User field group.
    * When an ACF User field group is selected, its associated custom fields are automatically added to the mapping repeater fields. Simply map the ACF fields to the form fields as needed.
    * Upon form submission, all custom ACF User fields are automatically updated and saved to the user profile. Manual publishing or saving of the user post is not necessary.

* Form Abandonment (Module)
  * Seamlessly integrates with the Bricks Form element.
  * Utilizes the form's 'save-submission' and 'email' actions to store records in the database and send an email to users regarding form abandonment.
  * Forms can be submitted normally or saved as a draft by providing an email address.
  * Form data is sanitized through Bricks form’s 'save-submission' action when creating a record.
  * Records can be viewed in the backend via Bricks Form Submission, with the form's status. (Planned support for 'View Form Submission in Frontend' in the Custom Element.)
  * If the form is submitted normally, it follows the default behavior.
  * If submitted as a draft (incomplete form), all filled-in data is saved. The user will receive an email containing a secure, time-based token URL.
  * If submitted as a draft, you may choose to exclude fields from saving.
  * Upon clicking the link, the user is automatically returned to the form page, where the token is validated, and the form is pre-filled with the saved data.
  * Subsequent submissions, whether normal or draft, will update the existing record in the database (no new entries), with proper data sanitization.

* Form Frontend Update ACF/WP options (Module)
  * Adds a custom form action: 'update-options'.
  * By default, only user roles with the 'manage_options' capability can update options.
  * Includes an option to exclude specific user roles from this form action.
  * Supports both WordPress and ACF options.
  * For WordPress options, you can update any option by providing the option name. For array-type options, you need to provide both the option name and the specific key.
  * Supports updating ACF options pages. You can select an ACF options page from the list, and the corresponding fields will automatically populate in the repeater mapping field.

* Form Multi Steps From (Module) (https://bricksbuilder.io/ideas/#4149)
  * Enabling Multi-Steps mode adds a new control 'step group' to all form field types.
  * Assign form fields to dedicated step group as needed.
  * Once all fields are assigned to step groups, enable step progress navigation bar. A step progress component using the native Bricks List Element is automatically created with all step groups.
  * Customize step progress icons and titles as needed.
  * You can jump straight to specific completed step by clicking step icon.
  * Option torreview the full form on the last step.
  * Option to show all form groups in Builder.

* Form Webhook (Module) (https://bricksbuilder.io/ideas/#4085)
  * Introduces a custom form action: 'webhook'.
  * Provides options to exclude specific form fields from data submission.
  * Includes a repeater field to add multiple webhook URLs.
  * Option to enable HMAC authentication in the request header.
  * Supports both POST and GET request methods.
  * Offers flexible data formats: FormData, JSON, Query String, or Multi-part.
  * Configurable cURL settings, including connection timeout and payload buffer size.
  * Allows setting a retry limit for failed cURL connections.
  * Utilizes multiple handlers with retry logic for managing webhook request calls efficiently.
  * Try Webhook here https://webhook.site/
  
* Confirmation Popup Modal Before Submission (Module)
  * Set form custom action 'Confirm Before Submit', Select your popup template.
  * When the user clicks send/submit on the form, a confirmation popup will automatically appear, but only if all validations are successfully passed. No actions will proceed until the user confirms the submission.

## Others
###### Lenis Smooth Scroll
* A new Lenis control group has been added under Settings → Page Settings in the Builder:
* Enable Lenis: Activate Lenis for specific pages as needed. Lenis assets are loaded only if enabled and exclusively on the frontend.
* Scroll Sync with GSAP ScrollTrigger: Includes an option to synchronize scrolling with GSAP's ScrollTrigger.

In-Progress
* Frontend Create Post Form ( Support ACF Custom Fields)
* Frontend Edit Post Form ( Support ACF Custom Fields)



  
## Preview
#### Custom Favorite Remote Templates Library
![Favorite Remote Templates](https://github.com/user-attachments/assets/ddeb05d5-3c71-4e51-b497-ebf89ece657b)

#### Query Manager
![Query Manager](https://github.com/user-attachments/assets/f5721e97-89b0-47d6-8dfd-e3b3055d049b)

#### Disable Icon Library Selection in Builder
![Disable Icon Library](https://github.com/user-attachments/assets/f417947b-7450-4617-95f9-dc0ffb6d8d2f)

#### New Escape Event Interactions && New Custom Field Template Condition
<img src="https://github.com/user-attachments/assets/b957f5e5-9455-4497-ab60-715475688213" width="48%">
<img src="https://github.com/user-attachments/assets/2bc8217d-3e5d-4145-8b38-14fb3c8d67c6" width="48%">

#### Core Framework Free Integration
![Core Framework](https://github.com/user-attachments/assets/63ef2fd7-9a94-4991-b9da-b0fb90965685)

#### Custom Framework Free Integration
![Custom Framework](https://github.com/user-attachments/assets/deb1c304-6dce-4df2-b741-e01c06e67dde)

#### View Form Submission In Frontend
![Form Submission](https://github.com/user-attachments/assets/4b48b9e4-d141-41c6-94fd-8d6b31f0b7f6)

#### 3D Model Viewer
![3d Model Viewer](https://github.com/user-attachments/assets/899fb964-bfb9-4169-8871-a6cffe4720a6)

#### Image Before After Comparison
![Image Before After](https://github.com/user-attachments/assets/3ce3d64b-8480-4958-87d0-2031f0174a37)

#### Image Hotspots
![Image Hotspots](https://github.com/user-attachments/assets/044221b9-2feb-4d38-b431-51686dfe5461)

#### Lottie Player
![Lottie](https://github.com/user-attachments/assets/52193988-9290-4f77-b309-14911c84c840)

#### Copy to Clipboard Button
![Copy to Clipboard Button](https://github.com/user-attachments/assets/feddfa15-ba96-4e7f-bc83-9c8c5fef4b6c)

#### Layout Elements (Section, Container, Block, Div) - Animate SVG Shape Divider on Scroll
![Animate Shape Divider](https://github.com/user-attachments/assets/272fb860-9761-4ef8-905b-4d7a76915cac)

#### Slider Nested
![Slider Nested](https://github.com/user-attachments/assets/a9edec06-d5b0-43d6-ab73-299bb844789d)

#### Tabs Nested
![Tabs Nested](https://github.com/user-attachments/assets/f1e890ed-208e-46c2-98bb-f2d0a91c06ea)

#### Back to Top with Progress
![Back to Top](https://github.com/user-attachments/assets/c6def6ab-c7e1-4c91-836f-7fe1f6110b36)

#### Form Icon Radio/Checkbox
![Icon Checkbox](https://github.com/user-attachments/assets/62d1b261-17c5-4824-82c9-cb7115da177a)

#### Form Signature Pad & Form File Upload Preview
<img src="https://github.com/user-attachments/assets/26a2a07e-8814-4661-9b45-2f0a76f32d06" width="48%">
<img src="https://github.com/user-attachments/assets/d5161709-548d-4c30-8b38-ce51d12cc6d1" width="48%">

#### Form Passowrd Field Pattern Validation & Visibility Toggle. Form Tel Fields International Tel Support
![Password and Tel Fields](https://github.com/user-attachments/assets/4f6f51fb-5186-4b18-8abb-c354334579e3)

#### Form Advance Datepicker
![Datepicker](https://github.com/user-attachments/assets/6c339fdb-eee8-4900-81ec-10773ebe3c37)

#### HTML Email Template Builder
![Email Template Builder](https://github.com/user-attachments/assets/7bc37bc6-9fdf-4de5-b7dc-383873bc00c5)

#### Form Advance User Registration
![Advance Registration](https://github.com/user-attachments/assets/20fef07c-c3d9-4d1b-a0ed-3618617481ce)

#### Form Webhook
![Webhook](https://github.com/user-attachments/assets/67c6e220-c852-4d43-aa69-820e4c3c2918)

#### Form Abandonment
![Form_Abandonment](https://github.com/user-attachments/assets/012f3f4c-f496-4f7f-955c-869f6c8cba72)

#### Form Update Options
![Update Options](https://github.com/user-attachments/assets/20b0fdf1-fd0d-49d6-889a-2566b8ca4a3b)

#### Form Multi Steps
![Multistep form](https://github.com/user-attachments/assets/8539a313-8a16-486f-a5bf-5aceae1332b4)

#### Form Frontend Form Update User Metas
![Update User Profile](https://github.com/user-attachments/assets/f718023d-ff9c-4473-82a9-72ac09b6ed81)

#### Form Frontend Form with Repeater Fields
![Repeater fields](https://github.com/user-attachments/assets/28179ae9-aa40-43c7-92a8-96e987e6f97f)

#### Offline Brickscodes Remote Templates
![Remote Templates](https://github.com/user-attachments/assets/14d7ed54-2241-4c58-af81-89a0d1db8358)

#### Plugin Update
![Update](https://github.com/user-attachments/assets/e2472580-4da5-4cac-91ab-8985a13fb989)
