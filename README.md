# Brickscodes
Elevate your Bricks Builder experience with this powerful plugin designed to seamlessly integrate with Bricks. Packed with custom elements, editor enhancements, custom conditions, custom dynamic tags, native elements enhancements, etc. This plugin extends the functionality of Bricks while adhering to WordPress best practices.

## Builder Tweaks
###### Compact Left Panel
* Expand/Collapse element category groups.
* Compact elements list.
###### Preview Global Class/Variables/Color Palette gird on hover 
###### Custom Builder Saved Message
###### Disable Elements by Template Type (Header/Footer/Content)
* Note: If the disabled elements are used in the Template/Content, Builder will throw warning error:generateCss: controls for element not found, and element/element controls are not rendered in Builder. Everything will work as normal in Builder and Frontend.
###### Builder Preloader Background Color
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

  
## Preview
#### Query Manager
![Query Manager](https://github.com/user-attachments/assets/f5721e97-89b0-47d6-8dfd-e3b3055d049b)

#### Core Framework Free Integration
![Core Framework](https://github.com/user-attachments/assets/63ef2fd7-9a94-4991-b9da-b0fb90965685)

#### View Form Submission In Frontend
![Form Submission](https://github.com/user-attachments/assets/4b48b9e4-d141-41c6-94fd-8d6b31f0b7f6)
