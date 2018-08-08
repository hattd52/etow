<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

trait ApiReponseFormatTrait
{
    /**
     * @var Array
     */
    protected $api_errors = [];

    /**
     * @var Array
     */
    protected $api_headers = [];

    /**
     * Reponse Listing with pagination
     * @param $arrayOrPagination
     */
    public function successWithArray($arrayOrPagination)
    {
        return $this->response($arrayOrPagination);
    }

    /**
     * Reponse Single Object
     * @param $object
     */
    public function successWithItem($object)
    {
        return $this->response($object);
    }

    /**
     * Response Authorization Error
     */
    public function errorUnauthorized($message_code = 'UNAUTHORIZED')
    {
        $this->addError(
            'unauthorized',
            trans('core::api.error:unauthorized')
        );

        return $this->response(null, $this->getFormattedErrors(), API_HTTP_UNAUTHORIZED, $message_code);
    }

    /**
     * Response Exception Error
     * @param $code
     * @param $message
     */
    public function errorException($code = 'system', $message = '', $message_code = 'SERVER_ERROR')
    {
        if (!$message) $message = trans('core::api.error:something went wrong');
        $this->addError($code, $message);

        return $this->response(null, $this->getFormattedErrors(), API_HTTP_SERVER_ERROR, $message_code);
    }

    /**
     * Response 404 Error
     * @param $code
     * @param $message
     */
    public function errorNotFound($code = 'system', $message = '', $message_code = 'NOT_FOUND')
    {
        if (!$message) $message = trans('core::api.error:404');
        $this->addError($code, $message);

        return $this->response(null, $this->getFormattedErrors(), API_HTTP_NOT_FOUND, $message_code);
    }

    /**
     * Response Forbidden Error
     * @param $code
     * @param $message
     */
    public function errorForbidden($code = 'permission', $message = '', $message_code = 'FORBIDDEN')
    {
        if (!$message) $message = trans('core::api.error:forbidden');
        $this->addError($code, $message);

        return $this->response(null, $this->getFormattedErrors(), API_HTTP_FORBIDDEN, $message_code);
    }

    /**
     * Response Bad Request Error
     * @param $code
     * @param $message
     */
    public function errorBadRequest($errors = [], $message_code = 'BAD_REQUEST')
    {
        foreach ($errors as $code => $message) {
            $this->addError($code, $message);
        }

        return $this->response(null, $this->getFormattedErrors(), API_HTTP_BAD_REQUEST, $message_code);
    }

    /**
     * Response Logic Error
     */
    public function error($code, $message, $message_code = 'FAILED_LOGIC')
    {
        $this->addError($code, $message);

        return $this->response(null, $this->getFormattedErrors(), API_HTTP_FAILED_LOGIC, $message_code);
    }

    /**
     * Response Resource not found Error
     * @param $code
     * @param $message
     */
    public function errorResourceNotFound($code = 'resource', $message = '', $message_code = 'RESOURCE_NOT_FOUND')
    {
        if (!$message) $message = trans('core::api.error:resource not found');
        $this->addError($code, $message);

        return $this->response(null, $this->getFormattedErrors(), API_HTTP_FAILED_LOGIC, $message_code);
    }

    /**
     * 
     */
    public function successWithMessage($translatedMessage = null)
    {
        if (!$translatedMessage) 
            $translatedMessage = trans("core::api.successully");

        return $this->response((object)['success_message' => $translatedMessage]);
    }

    /**
     * Helper push error to list
     * @param $domain 
     * @param $message
     */
    private function addError($domain, $message)
    {
        if ($domain && $message) $this->api_errors[$domain] = $message;
    }

    /**
     * Helper format errors as array
     */
    private function getFormattedErrors()
    {
        if (!count($this->api_errors)) return [];

        return [
            'first_error' => array_values($this->api_errors)[0],
            'all' => $this->api_errors
        ];
    }

    /**
     * Helper add header config
     */
    private function addHeader($key, $value)
    {
        if ($key && $value) $this->api_headers[$key] = $value;
    }

    /**
     * Helper headers as array
     */
    private function getHeaders()
    {
        return $this->api_headers;
    }

    /**
     * Helper format reponses
     */
    private function response($data = [], $errors = [], $status_code = API_HTTP_OK, $message_code = 'OK')
    {
        //Final array format
        $formatted = [
            'is_error'     => !!count($errors),
            'status_code'  => $status_code,
            'message_code' => $message_code,
            'errors'       => (object)$errors,
            'data'         => $data
        ];

        //Helpers
        function formatWithPaging(&$result, $collection) {
            $paging = [
                'total_item'   => $collection->total(),
                'per_page'     => $collection->perPage(),
                'current_page' => $collection->currentPage(),
                'total_pages'  => $collection->lastPage(),
            ];

            $result['data'] = [
                'list'   => $collection->items(),
                'paging' => $paging
            ];
        }

        function fromatWithoutPaging(&$result, $collection) {
            $result['data'] = ['list' => $collection];
        }

        //Array result
        if($data instanceof AbstractPaginator || $data instanceof Paginator){
            formatWithPaging($formatted, $data);
        }
        else if ($data instanceof AnonymousResourceCollection) {
            $hasPagination = false;

            if ($data->resource instanceof LengthAwarePaginator) {
                $hasPagination = true;
            }

            $hasPagination ? formatWithPaging($formatted, $data) : fromatWithoutPaging($formatted, $data);
        }
        else if (is_array($data)) {
            fromatWithoutPaging($formatted, $data);
        }
        //Single object result
        else {
            $formatted['data'] = $this->transformData($data);
        }

        //Meta data
        if ($data instanceof AnonymousResourceCollection) {
            if (isset($data->additional)) {
                $formatted['data']['meta'] = $data->additional;
            }
        }

        return response()->json($formatted, $status_code, $this->getHeaders());
    }

    /**
     * Helper format \Modules\Core\Transformers\Api\CoreResource item
     */
    private function transformData($data)
    {
        if (!$data) 
            return null;

        if (is_object($data) && method_exists($data, 'resourceFormat'))
            return $data->resourceFormat();
        
        return $data;
    }
}
