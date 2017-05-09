AkumaDistributionBundle
=====================

Implements additional way to load bundles without any updates of application files.

## Usage ##
Modify current kernel of application by extending (*extends AkumaKernel*) from `Akuma\Bundle\DistributionBundle\AkumaKernel`
and result should be merged with parent result, like following:
``` php
class AppKernel extends AkumaKernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            ...
        }

        //Following should be replaced to suport list of bundles from AkumaKernel
        return array_merge(parent::registerBundles(), $bundles);
    }
```

Add Resources/config/bundle.yml file to every bundle you want to be auto-registered within application's kernel:

``` yml
class: 'Acme\Bundle\TestBundle\AcmeTestBundle'
priority: -1
kernel: true
environment: ['test', 'prod']
```

Where :
* `class` - is a main class of `Bundle` 
* `priority` - is an integer value 
* `kernel` - indicates if bundle class require Kernel object as argument for constructor
* `environment` - array of environment names to load bundle



### Commands ###

#### List registered bundles ####
Command syntax is `akuma:debug:bundle`.
Displays all registered bundles within application.

#### Rebuild bundles cache ####
Command syntax is `akuma:distribution:rebuild`.
