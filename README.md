# Grid Component

Generic and reusable component refactoring the page module using the [FormEngine](https://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/FormEngine/) and [Fluid](https://github.com/TYPO3/Fluid).

## Terms

The terms introduced with this component reflect its independence of the underlining relations:

Grid item
  The matter in question which can be any [managed table](https://docs.typo3.org/typo3cms/InsideTypo3Reference/CoreArchitecture/Database/DatabaseStructure/).
Grid container
  Any managed table which holds the grid items and thus define their boundaries.
Grid template
  Divides the grid container’s space into grid areas, into which the grid items can be placed.

![](https://user-images.githubusercontent.com/1394346/30057918-0af65ba8-9239-11e7-8923-9bd9b0576e20.png)


In case of the page module the grid container is a page and its grid items are the content elements of this page. The grid template however is the backend layout whereby the grid areas are the page columns.

## Usage

Configure the tables for the grid container (`tx_my_domain_model_container`) and its items (`tx_my_domain_model_item`) using the TCA:

```php
$TCA['tx_my_domain_model_container'] = [
  // ... ,
  'columns' => [
    // ... ,
    'items' => [
      // ... ,
      'config' => [
        'type' => 'grid' ,
        'foreign_table' => 'tx_my_domain_model_item',
        'foreign_field' => 'container',
        'foreign_area_field' => 'area'
      ]
    ]
  ]
];
```

Define a grid template with three grid areas `Header`, `Content` and `Aside` using PageTsConfig:

```typoscript
tx_grid.tx_my_domain_model_container.items.template {

  rows = 2
  columns = 2

  areas {

    1 {
      title = Header  
      row.start = 1
      column {
        start = 1
        span = 2
      }
    }

    2 {
      title = Content
      row.start = 2
      column.start = 1
    }

    3 {
      title = Aside      
      row.start = 2
      column.start = 2
    }
  }
}
```

Integrate the view into a controller to render the container record `1`:

```php
$groups = $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine'];
$data = [
  'command' => 'edit',
  'tableName' => 'tx_my_domain_model_container',
  'vanillaUid' => 1,
  'customData' => [
    'tx_grid' => [
      'columnToProcess' => 'items',
      'containerProviderList' => $groups['layoutContainerGroup'],
      'itemProviderList' => $groups['contentElementGroup']
    ]
  ]
];
$group = GeneralUtility::makeInstance(GridContainerGroup::class) ;
$data = $group−>compile($data);

$factory = GeneralUtility::makeInstance(NodeFactory::class);
$layoutView = $factory−>create([
    'renderType' => 'layoutContainer'
] + $data)−>render();
```
