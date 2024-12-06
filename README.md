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
* Note: If the disabled elements are used in the Template/Content, Builder will throw warning error:generateCss: controls for element not found, and element/element controls are not rendered in Builder. Everything will work as normal in Builder and Frontend.
###### Builder Preloader Background Color
###### Favorite Remote Templates
* A favorite button is available on all remote templates.
* All templates marked as favorites will be added to your custom favorite remote template library.
* Remove favorite templates using either the favorite or remove button.
###### Query Manager
* First load automatically pulling all query settings from element across entire site and save as first query records in query manager modal.
* Save new query record - Set your query settings on element as usual, once finalized, click save, give a query name/tag/description and save.
* Apply existing query records - Open query manager modal, select from the record, click apply, query settings will populate to the element query control.
* Edit exisiting query records - Open query manager modal, modal will display current applied query record, change your element query settings as normal, once finalized, click edit, give a new name and description or make no changes, click Apply and Update. Query record with the same edited query id will get updated across entire site.
* If plugin deactivated or feature disabled, your elements query settings will continue to work. Saved query records will be reset.
###### Add New Elements Shortcut Bar
* New shortcut bar at Structure Panel to quickly add predefined common elements and auto set active to the newly added element.
###### Element Visibility in Builder/Frontend
* Apply Global Class 'bc-hide-in-canvas' to element, element will render in Frontend but hide in Builder. 
* Apply Global Class 'bc-hide-in-frontend' to element, element will not render in Frontend, element with red border render in Canvas.
* New action shortcut bar at Structure Panel to quickly toggle to check elements in 'bc-hide-in-canvas' and 'bc-hide-in-frontend.
###### Expand/Collapse All Children of Active Element
* New action shortcut bar at Structure Panel to quickly toggle to expand/collapse current active element at Structure Panel.
## Conditions
###### New Template Condition Control
* New control allow you to use dynamic data from custom field, compare the value you defined and add the score 10 to template.
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
###### Core Framework Free Integration
* Auto sync changes that made on Core Framework settings page to Builder.
* All Core Framework classes and variable are auto import to Builder.
* Separate modal for Core Framework Variables.
* When deactivate plugin, you may choose to keep Core Framework.
* Core Framework dark/light mode toggle in Builder.
###### Custom Framework Integration
* Upload your custom framework stylesheet, let Brickscodes automatically import Classes and Variables in to Builder and enqueue stylesheet in Canvas and Frontend
## Custom Elements
###### 3D Model Viewer
* Support glb/gltf, model animation, lazy load, camera control, etc.
###### Copy to Clipboard Button
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
* Support advance table with export, pagination, column reorder, responsive.
## Enhance Native Bricks Elements
###### Slider Nested
* Support Splide Extension UrlHashNavigation, AutoScroll, Intersection. Extension assets only load if enabled.
* Support sync sliders.
* Number/fraction pagination.
* Progress Bar for overall progress and slide duration progress.
* Play/Pause button for autoplay and autoscroll.
* Disable Slider at selected breakpoint.
###### Back to Top
* Add scroll progress to Back to Top Element.
* When enabled, adding a Back to Top element will automatically include a scroll progress indicator. Customize its style as desired.
###### Form
* Icon Radio/Checkbox
* Extra HTML5 pattern attribute for Password and Email field type. Validate in frontend with pure HTML5 validation and backend with form validation hook.
* Signature Pad with File Preview. Seamlessly integrates with the Bricks Form element. Support viewport resize and redraw signed signature. Use form field type 'text' as Signature Pad input. Once enabled, it will automatically create field type 'file' for preview and upload. Assets only loaded if enabled.
* File uploads thumbnail preview. Auto filter out duplication. Seemless integrate with Bricks Form File type.
* Offline Brickscodes Remote Templates are accessible only if you activate the Form Abandonment or Confirmation Popup Modal Before Submission module. Save the sample popup template as a popup template type and style it according to your preferences. However, it is essential not to alter the JavaScript event interactions for the popup button.

* Advance User Registration (Module)
  * Seamlessly integrates with Bricks' Form default User Registration action.
  * Supports ACF fields.
  * Offers an option to validate user email addresses using a secure, time-based activation link before creating the user account.
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

* Confirmation Popup Modal Before Submission (Module)
  * Set form custom action 'Confirm Before Submit', Select your popup template.
  * When the user clicks send/submit on the form, a confirmation popup will automatically appear, but only if all validations are successfully passed. No actions will proceed until the user confirms the submission.

In-Progress
* Webhook
* Frontend Update User Profile Form ( Support ACF Custom Fields)
* Frontend Create Post Form ( Support ACF Custom Fields)
* Frontend Edit Post Form ( Support ACF Custom Fields)



  
## Preview
#### Custom Favorite Remote Templates Library
![Favorite Remote Templates](https://github.com/user-attachments/assets/ddeb05d5-3c71-4e51-b497-ebf89ece657b)

#### Query Manager
![Query Manager](https://github.com/user-attachments/assets/f5721e97-89b0-47d6-8dfd-e3b3055d049b)

#### Core Framework Free Integration
![Core Framework](https://github.com/user-attachments/assets/63ef2fd7-9a94-4991-b9da-b0fb90965685)

#### Custom Framework Free Integration
![Custom Framework](https://github.com/user-attachments/assets/deb1c304-6dce-4df2-b741-e01c06e67dde)

#### View Form Submission In Frontend
![Form Submission](https://github.com/user-attachments/assets/4b48b9e4-d141-41c6-94fd-8d6b31f0b7f6)

#### 3D Model Viewer
![3d Model Viewer](https://github.com/user-attachments/assets/899fb964-bfb9-4169-8871-a6cffe4720a6)

#### Copy to Clipboard Button
![Copy to Clipboard Button](https://github.com/user-attachments/assets/feddfa15-ba96-4e7f-bc83-9c8c5fef4b6c)

#### Slider Nested
![Slider Nested](https://github.com/user-attachments/assets/a9edec06-d5b0-43d6-ab73-299bb844789d)

#### Back to Top with Progress
![Back to Top](https://github.com/user-attachments/assets/c6def6ab-c7e1-4c91-836f-7fe1f6110b36)

#### Form Icon Radio/Checkbox
![Icon Checkbox](https://github.com/user-attachments/assets/62d1b261-17c5-4824-82c9-cb7115da177a)

#### Form Signature Pad & Form File Upload Preview
<img src="https://github.com/user-attachments/assets/26a2a07e-8814-4661-9b45-2f0a76f32d06" width="48%">
<img src="https://github.com/user-attachments/assets/d5161709-548d-4c30-8b38-ce51d12cc6d1" width="48%">

#### Form Advance User Registration
![Advance Registration](https://github.com/user-attachments/assets/20fef07c-c3d9-4d1b-a0ed-3618617481ce)

#### Offline Brickscodes Remote Templates
![Remote Templates](https://github.com/user-attachments/assets/14d7ed54-2241-4c58-af81-89a0d1db8358)

#### Form Abandonment
![Form_Abandonment](https://github.com/user-attachments/assets/012f3f4c-f496-4f7f-955c-869f6c8cba72)

#### Plugin Update
![Update](https://github.com/user-attachments/assets/e2472580-4da5-4cac-91ab-8985a13fb989)
