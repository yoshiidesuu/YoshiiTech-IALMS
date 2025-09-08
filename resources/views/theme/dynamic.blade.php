{{-- Dynamic Theme CSS --}}
<style>
{!! app('App\Http\Controllers\Admin\ThemeController')->generateCSS() !!}
</style>