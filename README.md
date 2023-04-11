# Search API Solr Field for Access Control with Group
This module add a field processed to determine access control based on Group

# Installation
By composer: 

````
composer require digitalutsc/group_solr
````

## Work with Federated Search

- Required modules: 
  * Federated Search Front-end user interface: https://github.com/digitalutsc/drupal_ajax_solr 
  * Add a Search Api Solr field for Access Control with Group: https://github.com/digitalutsc/group_solr

- In `/admin/config/search/search-api/index/default_solr_index/fields`, Click Add fields > General > Group: Access Control (search_api_group_access_control) 

## How does it work ?
  - Every time a node or media is indexed to Solr, this field will be processed by checking the access control configuration which is setup with Group module. It will determine the entity to be public or private for annonymous users
  - Field's values to be indexed to Solr:
    - Public: 200 
    - Private: 403
