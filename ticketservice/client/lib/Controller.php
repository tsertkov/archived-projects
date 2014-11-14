<?php
/**
 * @category TicketSellingClient
 * @package Controller
 */

namespace tc;

class Controller
{
    /**
     * @var ResourceContainer
     */
    protected $_resourceContainer;

    /**
     * @var \Jaer\View
     */
    protected $_view;

    /**
     * @param ResourceContainer $resourceContainer
     */
    public function __construct(ResourceContainer $resourceContainer)
    {
        $this->_resourceContainer = $resourceContainer;
        $this->_view = $resourceContainer->getView();
        $this->_view->baseUri = $this->_getBaseUri();
        $this->indexAction();
    }

    /**
     * @return string
     */
    protected function _getBaseUri()
    {
        return str_replace(array('\\', '//'), '/', dirname($_SERVER['SCRIPT_NAME']) . '/');
    }

    public function indexAction()
    {
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $this->_processFormSubmit();
        }

        $this->_view->render('index');
    }

    private function _processFormSubmit()
    {
        $this->_view->requestedSeats = $requestedSeats = trim($_POST['requestedSeats']);
        
        try {
          $service = $this->_resourceContainer->getServiceFactory()->getReservationService();
          
          if ('book' == $_POST['submit']) {
              $seats = $this->_view->seats = $_POST['seats'];
              $name = $this->_view->name = $_POST['name'];
              $service->reserveSeats($name, $seats);

              // set success message and reset suggested seats
              $this->_view->success = sprintf('Seats %s are booked by %s', implode(', ', $seats)
                  , $name);
              $this->_view->seats = null;
              
          } else if ('suggest' == $_POST['submit']) {
              $this->_view->seats = $service->getFreeSeatsList($requestedSeats);
          }
        } catch (\tsSDK\Client_Exception $e) {
            $this->_view->errorType = 'Webservice client error';
            $this->_view->error = $e->getMessage();
            return;

        } catch (\tsSDK\Service_Exception $e) {
            $this->_view->errorType = 'Service error';
            $this->_view->error = $e->getMessage();
            return;

        } catch (\tsSDK\Server_Exception $e) {
            $this->_view->errorType = 'Remote server internal error';
            $this->_view->error = $e->getMessage();
            return;
        }
    }
}