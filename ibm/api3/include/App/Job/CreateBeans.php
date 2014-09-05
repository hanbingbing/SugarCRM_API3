<?php
include_once "custom/include/Decorator/DecoratorFactory.php";

abstract class App_Job_CreateBeans
{
    /*
     * create a bean for use in setup. These will be automatically
     * cleaned up at end of run
     */
    public function createBean($beanName, $id = '', $args = '')
    {
        static $count = 0;
        $decorator = DecoratorFactory::getDecorator($beanName, $id);
        if (empty($id)) {
            $id = "$beanName" . $count;
            $count++;
        }
        $decorator = DecoratorFactory::getDecorator($beanName);
        $decorator->id = $id;
        $decorator->new_with_id = true;
        $decorator->deleted = 0;
        if (is_array($args)) {
            foreach ($args as $k => $v) {
                $decorator->$k = $v;
            }
        }
        $decorator->save();
        $table = $decorator->getTableName();
        $id = $decorator->id;

        if (!empty($table)) {
            $sql = "DELETE FROM $table WHERE id = '$id'";
            $this->cleanupSQL[] = $sql;

            if ($decorator->hasCustomFields()) {
                $sql = "DELETE FROM $table" . "_cstm WHERE id_c = '$id'";
                $this->cleanupSQL[] = $sql;
            }
        }
        return $decorator;
    }

    /**
     * @param $relationship - relationship name
     * @param $lhs          - sugarbean
     * @param $rhs         -   sugarbean
     * @param $attributes  - other attributes in relationship
     */
    public function relationshipAdd($relationship, $lhs, $rhs, $attributes = array())
    {
        $lhs->load_relationship($relationship);
        $lhs->$relationship->add($rhs->id, $attributes, true);
        $relElement = array('rel' => $relationship, 'lhs' => $lhs, 'rhs' => $rhs);
        array_push($this->cleanupReleationships, $relElement);
    }

    /*
     * create an calls bean for use in setup
     */
    public function createEmails($id = '', $args = array())
    {
        $bean = $this->createBean("Emails", $id, $args);
        $this->cleanupSQL[] = "DELETE FROM emails_text WHERE email_id = '{$bean->id}'";
        return $bean;
    }

    /*
     * create a user bean for use in setup
     */
    public function createUser($id = '', $args = array())
    {
        if (!isset($args['first_name'])) {
            $args['first_name'] = 'Temp';
        }
        if (!isset($args['last_name'])) {
            $args['last_name'] = 'User';
        }
        if (!isset($args['user_name'])) {
            if (empty($id)) {
                $args['user_name'] = $id;
            } else {
                $args['user_name'] = 'tempUser';
            }
        }
        if (!isset($args['status'])) {
            $args['status'] = 'Active';
        }
        if (!isset($args['address_country'])) {
            $args['address_country'] = 'US';
        }
        return $this->createBean("Users", $id, $args);
    }


    /*
     * create a contact bean for use in setup
     */
    public function createContact($id = '', $args = array())
    {
        if (!isset($args['first_name'])) {
            $args['first_name'] = 'Temp';
        }
        if (!isset($args['last_name'])) {
            $args['last_name'] = 'Contact';
        }
        if (!isset($args['primary_address_country'])) {
            $args['primary_address_country'] = 'US';
        }

        return $this->createBean("Contacts", $id, $args);
    }

    /*
     * create an opportunity bean for use in setup
     */
    public function createOpportunity($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Opportunity";
        }
        if (!isset($args['lead_source'])) {
            $args['lead_source'] = 'BRSP';
        }
        if (!isset($args['sales_stage'])) {
            $args['sales_stage'] = '03';
        }
        if (!isset($args['description'])) {
            $args['description'] = 'Description for ' . $args['name'];
        }
        return $this->createBean("Opportunities", $id, $args);
    }

    /**
     * create a currency bean for use in setup
     */
    public function createCurrency($id = '', $args = array())
    {
        if (!isset($args['iso4217'])) {
            $args['iso4217'] = substr($id, 0, 3);
        }
        if (!isset($args['conversion_rate'])) {
            $args['conversion_rate'] = 1;
        }
        return $this->createBean('Currencies', $id, $args);
    }

    /*
     * create an account bean for use in setup
     */
    public function createAccount($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Account";
        }
        if (!isset($args['billing_address_country'])) {
            $args['billing_address_country'] = 'US';
        }
        if (!isset($args['shipping_address_country'])) {
            $args['shipping_address_country'] = 'US';
        }
        $bean = $this->createBean("Accounts", $id, $args);
        $sql = "DELETE FROM accounts_hierarchy WHERE account_id = '" . $bean->id . "'";
        $this->cleanupSQL[] = $sql;
        return $bean;
    }

    /*
     * create a line item bean for use in setup
     */
    public function createLineItem($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Revenue Line Item";
        }
        if (!isset($args['level10'])) {
            $args['level10'] = 'B6000';
        }
        if (!isset($args['duration'])) {
            $args['duration'] = '1';
        }
        if (!isset($args['probability'])) {
            $args['probability'] = '50';
        }
        if (!isset($args['revenue_amount'])) {
            $args['revenue_amount'] = '123456789';
        }
        if (!isset($args['description'])) {
            $args['description'] = 'Description for ' . $args['name'];
        }
        return $this->createBean("ibm_RevenueLineItems", $id, $args);
    }

    /*
     * create a roadmaps bean for use in setup
     */
    public function createRoadmaps($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Roadmaps";
        }
        if (!isset($args['roadmaps_status'])) {
            $args['roadmaps_status'] = 'ROADSTR';
        }
        if (!isset($args['timeperiod_id'])) {
            $args['timeperiod_id'] = 'tps2014Q1';
        }
        if (!isset($args['probability'])) {
            $args['probability'] = '50';
        }
        if (!isset($args['revenue_type'])) {
            $args['revenue_type'] = 'Transactional';
        }
        if (!isset($args['forecast_date'])) {
            $args['forecast_date'] = '2020-01-01';
        }
        if (!isset($args['revenue_amount'])) {
            $args['revenue_amount'] = '123456789';
        }
        return $this->createBean("ibm_Roadmaps", $id, $args);
    }

    /*
     * create a DetailedStatus bean for use in setup
     */

    public function createIbmDetailedStatus($id = '', $args = array())
    {
        //Please add Required fields
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp DetailedStatus";
        }
        if (!isset($args['date_entered'])) {
            $args['date_entered'] = '05/20/2014';
        }
        if (!isset($args['date_modified'])) {
            $args['date_modified'] = '05/20/2014';
        }
        if (!isset($args['description'])) {
            $args['description'] = 'Test Description';
        }
        if (!isset($args['deleted'])) {
            $args['deleted'] = '0';
        }

        return $this->createBean("ibm_DetailedStatus", $id, $args);
    }

    /*
     * create a Stepstoclosure bean for use in setup
     */

    public function createIbmStepstoclosure($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Stepstoclosure";
        }

        if (!isset($args['date_entered'])) {
            $args['date_entered'] = '05/22/2014';
        }

        if (!isset($args['date_modified'])) {
            $args['date_modified'] = '05/22/2014';
        }

        if (!isset($args['description'])) {
            $args['description'] = 'Test';
        }

        if (!isset($args['comments'])) {
            $args['comments'] = 'Test Comments';
        }

        return $this->createBean("ibm_Stepstoclosure", $id, $args);
    }

    /*
     * create a adjustments bean for use in setup
     */
    public function createAdjustments($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Adjustments";
        }
        if (!isset($args['roadmaps_status'])) {
            $args['roadmaps_status'] = 'ROADSTR';
        }
        if (!isset($args['timeperiod_id'])) {
            $args['timeperiod_id'] = 'tps2014Q1';
        }
        if (!isset($args['solution_id'])) {
            $args['solution_id'] = 'offering';
        }
        if (!isset($args['revenue_type'])) {
            $args['revenue_type'] = 'Transactional';
        }
        if (!isset($args['forecast_date'])) {
            $args['forecast_date'] = '2020-01-01';
        }
        if (!isset($args['revenue_amount'])) {
            $args['revenue_amount'] = '123456789';
        }
        return $this->createBean("ibm_Adjustments", $id, $args);
    }

    /*
     * create a targets bean for use in setup
     */
    public function createTargets($id = '', $args = array())
    {
        if (!isset($args['timeperiod_id'])) {
            $args['timeperiod_id'] = 'tps2014Q1';
        }
        if (!isset($args['revenue_type'])) {
            $args['revenue_type'] = 'Transactional';
        }
        if (!isset($args['amount'])) {
            $args['amount'] = '123456789';
        }
        return $this->createBean("ibm_Targets", $id, $args);
    }

    /*
     * create a CMR bean for use in setup
     */
    public function createCMR($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp CMR";
        }
        if (!isset($args['site_id'])) {
            $args['site_id'] = '123';
        }
        if (!isset($args['cmr_number'])) {
            $args['cmr_number'] = '1';
        }
        /*
         * not needed now
        if (!isset($args['issuing_country'])) {
            $args['issuing_country'] = 'US';
        }
         */
        if (!isset($args['primary'])) {
            $args['primary'] = '1';
        }
        return $this->createBean("ibm_CMR", $id, $args);
    }


    /*
     * create an tasks bean for use in setup
     */
    public function createTasks($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Tasks";
        }
        if (!isset($args['priority'])) {
            $args['priority'] = 'Medium';
        }
        if (!isset($args['status'])) {
            $args['status'] = 'In Progress';
        }
        return $this->createBean("Tasks", $id, $args);
    }

    /*
     * create an calls bean for use in setup
     */
    public function createCalls($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Calls";
        }
        if (!isset($args['date_start_date'])) {
            $args['date_start_date'] = '05/07/2013 09:36am';
        }
        if (!isset($args['duration_minutes'])) {
            $args['duration_minutes'] = '30';
        }
        if (!isset($args['status'])) {
            $args['status'] = 'Planned';
        }
        return $this->createBean("Calls", $id, $args);
    }

    /*
     * create a line item bean for use in setup
     */
    public function createIbmProduct($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Ibm Product";
        }
        if (!isset($args['level'])) {
            $args['level'] = '0';
        }
        if (!isset($args['parent_id'])) {
            $args['parent_id'] = '';
        }
        if (!isset($args['source_parent_id'])) {
            $args['source_parent_id'] = '';
        }
        if (!isset($args['offering_type'])) {
            $args['offering_type'] = '';
        }
        if (!isset($args['category'])) {
            $args['category'] = '';
        }
        if (!isset($args['description'])) {
            $args['description'] = 'no description';
        }
        if (!isset($args['deleted'])) {
            $args['deleted'] = '0';
        }
        if (!isset($args['type'])) {
            $args['type'] = 'product';
        }
        if (!isset($args['om_brand_code'])) {
            $args['om_brand_code'] = '';
        }

        return $this->createBean("ibm_Products", $id, $args);
    }

    /*
     * create a IBM Bussiness Parteners in setup
     */
    public function createIbmBusinessParteners($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp IBM Business Partners";
        }
        return $this->createBean("ibm_BusinessPartners", $id, $args);
    }

    /*
     * create a IBM Solutions in setup
     */
    public function createIbmSolutions($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp IBM Solutions";
        }
        if (!isset($args['category'])) {
            $args['category'] = 'Value Creation';
        }
        if (!isset($args['activity_status'])) {
            $args['activity_status'] = 'Active';
        }
        return $this->createBean("ibm_Solutions", $id, $args);
    }

    /*
    * create an ibm_CampaignCode bean for use in setup
    */
    public function createIbmCampaignCode($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp ibm_CampaignCode";
        }
        $bean = $this->createBean("ibm_CampaignCode", $id, $args);
        return $bean;
    }

    /*
    * create an ibm_Addresses bean for use in setup
    */
    public function createIbmAddresses($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp ibm_Addresses";
        }
        $bean = $this->createBean("ibm_Addresses", $id, $args);
        return $bean;
    }

    /*
     * create a line item bean for use in setup
     */
    public function createBusinessPartner($id = '', $args = array())
    {
        if (isset($id)) {
            $args['name'] = $id;
        } else {
            $args['name'] = "Temp Business Partner";
        }
        if (isset($args['om_ownership_enabled'])) {
            $args['om_ownership_enabled'] = 'CRMOME';
        }
        if (!isset($args['description'])) {
            $args['description'] = 'Description for ' . $args['name'];
        }
        return $this->createBean("ibm_BusinessPartners", $id, $args);
    }

    /*
     * create a TimePeriod bean for use in setup
     */
    public function createTimePeriod($id = '', $args = array())
    {
        if (!isset($args['name'])) {
            if (isset($id)) {
                $args['name'] = $id;
            } else {
                $args['name'] = "tps2020";
            }
        }
        if (!isset($args['start_date'])) {
            $args['start_date'] = '2020-01-01';
        }
        if (!isset($args['end_date'])) {
            $args['end_date'] = '2020-12-31';
        }
        return $this->createBean("TimePeriods", $id, $args);
    }
}
