## YADL (Your Activities of Daily Living)

Repository for the server of YADL (Your Activities of Daily Living), *the small data lab@Cornell Tech*

#### Main Files

**index.html**: Main HTML file.

**customJS/adl.js**: Main Javascript file.

**customCSS/adl.css**: Main CSS file.

**adl_backend/**: All the backend code written in PHP.

#### Workflow of Client-Server Communication

> Step 1: Client randomly creates a *client_id* and send a **POST** to **adl_backend/init.php**

In this step, server will register the *client_id* and any subsequent **POST** packet should contain this field to be considered as authorized and valid.

> Step 2: Ohmage Authorization.

After user click *Ohmage Signin* Button, client sends a **GET** packet to **adl_backend/auth_callback.php** that will redirect the user to Ohmage. After this process is success, the user will be redirected to this page.

> Step 3: Client Image Pulling.

Each time that user wants to pull an image from server, the client should make the following **POST** packet to *adl_backend/img_query.php*:

  **choice**: "0"(Easy) OR "1"(Medium) OR "2"(Hard) *-Based on current user selection*
  
  **img**: *current image index (1 to N)*
  
  **img_src**: *image source file (url)*
  
  **client_id**: *client_id*

> Step 4: Server Feedback.

For each **POST** packet to *adl_backend/img_query.php*, the server will make the following feedback JSON to client:

  **previous_image_src**: *last acitivity image that user encountered*
  
  **image_number**: *next image index (1 to N)*
  
  **image_src**: *next image url*
  
  **total_image_number**: *total number of images that user is supposed to encounter*
  
  **hard_list**: *hard activity images that user has selected untill now*
