<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Requests\TagRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class TagsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!Gate::allows('tags.view')) {
            abort(403);
        }

        $tags = Tag::paginate();

        return view('tags.index', [
            'title' => 'Tags List',
            'tags' => $tags,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        if (Gate::denies('tags.create')) {
            abort(403);
        }

        return view('tags.create', [
            'tag' => new Tag(),
        ]);
    }

    public function store(TagRequest $request)
    {
        
        $request->merge([
            'slug' =>  Str::slug($request->name)
        ]);
        
        $tag = Tag::create($request->all());

   
        return redirect('/tags')->with('success', 'Tag created!');
    }

    public function edit($id)
    {
        Gate::authorize('tags.edit');

        $tag = Tag::findOrFail($id);
      

        return view('tags.edit', [
            'tag' => $tag,
        ]);
    }

    public function update(Request $request, $id)
    {

        $data = $this->validateRequest($request, $id);
        dd($data, $request->all());

        $tag = Tag::findOrFail($id);
    
        $tag->update([
            'name' => $data['name'],
            'slug' => Str::slug($request->input('name')),
        ]);

        Session::flash('success', 'Tag updated!');
        Session::flash('info', $tag->name);

        return redirect('/tags'); //->with('success', 'Tag updated!');
    }

    public function destroy($id)
    {
        Tag::destroy($id);


        return redirect('/tags')->with('success', 'Tag deleted!');
    }

    protected function validateRequest(Request $request, $id = 0)
    {
        $rules = [
            'name' => ['required', 'string', 'between:3,255', "unique:tags,name,$id"],
        ];
        $messages = [
            'required' => 'The input field :attribute is mandatory',
        ];

        
        $validator = Validator::make(
            $request->all(),
            $rules,
            $messages,
            [
                'name' => 'Tag Name'
            ]
        );

        if ($validator->failed()) {
            return redirect()->back();
        }

        $clean = $validator->validate();
        return $clean;
    }
}
