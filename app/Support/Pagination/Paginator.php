<?php

namespace App\Support\Pagination;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Illuminate\Support\Collection;

class Paginator implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * All of the items being paginated.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    /**
     * The total number of items before slicing.
     *
     * @var int
     */
    protected $total;

    /**
     * The current page being "viewed".
     *
     * @var int
     */
    protected $currentPage;
    
    /**
     * The parameters to add to all URLs.
     *
     * @var array
     */
    protected $params = [];
    
    /**
     * The number of links to display on each side of current page link.
     *
     * @var int
     */
    public $onEachSide = 3;

    /**
     * The query string variable used to store the page.
     *
     * @var string
     */
    protected $pageName = 'page';

    /**
     * Create a new paginator instance.
     *
     * @param  mixed  $items       Items
     * @param  int    $total       Total
     * @param  int    $currentPage Current pag
     * @param  int    $perPage     Items per page
     * 
     * @return void
     */
    public function __construct($items, int $total, int $perPage, int $currentPage, string $pageName = 'page', int $onEachSide = 3)
    {
        $this->total = $total;
        $this->pageName = $pageName;
        $this->onEachSide = $onEachSide;

        $this->perPage = $perPage > 0? $perPage: 1;
        $this->lastPage = max((int) ceil($total / $perPage), 1);
        $this->items = $items instanceof Collection ? $items : Collection::make($items);
        
        $this->setCurrentPage($currentPage);
    }

    /**
     * Set the current page for the request.
     *
     * @param  Slim\Http\Request  $request Request
     * 
     * @return void
     */
    protected function setCurrentPage(int $currentPage)
    {
        $this->currentPage = abs($currentPage) > 0? $currentPage: 1;
    }

    /**
     * Add a set of params string values to the paginator.
     *
     * @param  array|string  $key
     * @param  string|null  $value
     * 
     * @return $this
     */
    public function appends($key, $value = null)
    {
        if (is_array($key)) {
            return $this->appendArray($key);
        }

        return $this->addParam($key, $value);
    }

    /**
     * Add an array of params string values.
     *
     * @param  array  $keys
     * 
     * @return $this
     */
    protected function appendArray(array $keys)
    {
        foreach ($keys as $key => $value) {
            $this->addParam($key, $value);
        }

        return $this;
    }

    /**
     * Add a param string value to the paginator.
     *
     * @param  string  $key
     * @param  string  $value
     * 
     * @return $this
     */
    protected function addParam($key, $value)
    {
        if ($key !== $this->pageName) {
            $this->params[$key] = $value;
        }

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Get the slice of items being paginated.
     *
     * @return array
     */
    public function items(): array
    {
        return $this->items->all();
    }

    /**
     * Get the number of the first item in the slice.
     *
     * @return int
     */
    public function firstItem(): int
    {
        return count($this->items) > 0 ? ($this->currentPage - 1) * $this->perPage + 1 : null;
    }

    /**
     * Get the number of the last item in the slice.
     *
     * @return int
     */
    public function lastItem(): int
    {
        return count($this->items) > 0 ? $this->firstItem() + $this->count() - 1 : null;
    }

    /**
     * Get the number of items shown per page.
     *
     * @return int
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages(): bool
    {
        return $this->currentPage() != 1 || $this->hasMorePages();
    }

    /**
     * Determine if the paginator is on the first page.
     *
     * @return bool
     */
    public function onFirstPage(): bool
    {
        return $this->currentPage() <= 1;
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->items->getIterator();
    }

    /**
     * Determine if the list of items is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * Determine if the list of items is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return $this->items->isNotEmpty();
    }

    /**
     * Get the number of items for the current page.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Get the paginator's underlying collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCollection(): Collection
    {
        return $this->items;
    }

    /**
     * Set the paginator's underlying collection.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * 
     * @return $this
     */
    public function setCollection(Collection $collection)
    {
        $this->items = $collection;

        return $this;
    }

    /**
     * Determine if the given item exists.
     *
     * @param  mixed  $key
     * 
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->items->has($key);
    }

    /**
     * Get the item at the given offset.
     *
     * @param  mixed  $key
     * 
     * @return mixed
     */
    public function offsetGet($key): bool
    {
        return $this->items->get($key);
    }

    /**
     * Set the item at the given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * 
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->items->put($key, $value);
    }

    /**
     * Unset the item at the given key.
     *
     * @param  mixed  $key
     * 
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->items->forget($key);
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return $this->currentPage() < $this->lastPage();
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function lastPage()
    {
        return $this->lastPage;
    }

    /**
     * Get the array of elements to pass to the view.
     *
     * @return array
     */
    public function elements(): array
    {
        $window = UrlWindow::make($this);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }

    /**
     * Get the previous page.
     *
     * @return int
     */
    public function previousPageUrl(): string
    {
        if ($this->currentPage() > 1) {
            return $this->url($this->currentPage() - 1);
        }
    }

    /**
     * Get the next page.
     *
     * @return int
     */
    public function nextPageUrl(): string
    {
        if ($this->lastPage() > $this->currentPage()) {
            return $this->url($this->currentPage() + 1);
        }
    }

    /**
     * Create a range of pagination URLs.
     *
     * @param  int  $start
     * @param  int  $end
     * 
     * @return array
     */
    public function getUrlRange($start, $end): array
    {
        $urls = [];

        foreach(range($start, $end) as $page) {
            $urls[$page] = $this->url($page);
        }

        return $urls;
    }

    /**
     * Get the URL for a given page number.
     *
     * @param  int  $page
     * 
     * @return string
     */
    public function url(int $page): string
    {
        if ($page <= 0) {
            $page = 1;
        }

        // If we have any extra query string key / value pairs that need to be added
        // onto the URL, we will put them in query string form and then attach it
        // to the URL. This allows for extra information like sortings storage.
        $parameters = [$this->pageName => $page];
        
        if (count($this->params) > 0) {
            $parameters = array_merge($this->params, $parameters);
        }

        return http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);
    }
}
