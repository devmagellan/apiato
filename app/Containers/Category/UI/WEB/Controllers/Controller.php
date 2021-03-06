<?php

namespace App\Containers\Category\UI\WEB\Controllers;

use App\Containers\Category\UI\WEB\Requests\CreateCategoryRequest;
use App\Containers\Category\UI\WEB\Requests\DeleteCategoryRequest;
use App\Containers\Category\UI\WEB\Requests\GetAllCategoriesRequest;
use App\Containers\Category\UI\WEB\Requests\FindCategoryByIdRequest;
use App\Containers\Category\UI\WEB\Requests\UpdateCategoryRequest;
use App\Containers\Category\UI\WEB\Requests\StoreCategoryRequest;
use App\Containers\Category\UI\WEB\Requests\EditCategoryRequest;
use App\Ship\Parents\Controllers\WebController;
use Apiato\Core\Foundation\Facades\Apiato;

/**
 * Class Controller
 *
 * @package App\Containers\Category\UI\WEB\Controllers
 */
class Controller extends WebController
{
    private $parent_num=0;
    public $all_subcats=[];
    public $all_parcats=[];
    public $level=0;


  /**
   * Show all entities
   *
   * @param GetAllCategoriesRequest $request
   */
  public function index(GetAllCategoriesRequest $request, $id)
  {
    //$categories = Apiato::call('Category@GetAllCategoriesAction', [$request]);
      $categories= Apiato::call('Site@GetAllProductCategoriesAction', [$request]);
      $categoriesOnlyRoot = $categories->where('parent_id', 0);
      $user=null;
      if(\Auth::user()){
          $user=\App\Containers\User\Models\User::where('id',\Auth::user()->id)->first();}


      $currentPage = $request->input('page');
      $from=$request->input('price_start');
      $to=$request->input('price_end');
      $sort_by_date=$request->input('sort_by_date');
      // Make sure that you call the static method currentPageResolver()
      // before querying users
      \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
          return $currentPage;
      });

      '';
      $q= \App\Containers\Ad\Models\Ad::where('category_id',$id)->with('pictures')->where(function ($query) use($request) {
          $query->where('message', 'like', '%' . $request->input('search_string') . '%')
              ->orWhere('title', 'like', '%' . $request->input('search_string') . '%');
      })
          ->where(function ($query) use($request) {
              $query->where('message', 'like', '%' . $request->input('rubric_search') . '%')
                  ->orWhere('title', 'like', '%' . $request->input('rubric_search') . '%');
          })


          ->where('active',1);

      if($request->input('sort_by_date')=='low_to_high'){
          dump('low_to_high');
          $q->orderBy('created_at');
      }
      if($request->input('sort_by_date')=='high_to_low'){
          dump('high_to_low');
          $q->orderByDesc('created_at');
      }



      $qr=clone($q);
$pricesLimits=$qr->select( \DB::raw("MAX(ads.price) AS max_price"), \DB::raw("MIN(ads.price) AS min_price"))->get()->toArray();
if($pricesLimits[0]['max_price']==$pricesLimits[0]['min_price']){
    $pricesLimits[0]['min_price']=0;
}
      $products=$q->where(function ($query) use($from,$to) {
      if(!empty($from) && !empty($to)){
          $query->where('price','>=',$from)
              ->where('price','<=',$to);
      }
  })
          /*->where('city',$request->input('city'))->where('administrative',$request->input('administrative'))*/

          ->paginate(5);
    //$products=\App\Containers\Ad\Models\Ad::where('category_id',$id)->with('pictures')->paginate(4);
      return view('category::catalog',  compact('products','categoriesOnlyRoot', 'categories','user','pricesLimits'));
   // return $link . ' ' . $id;
  }

  /**
   * Show one entity
   *
   * @param FindCategoryByIdRequest $request
   */
  public function show(FindCategoryByIdRequest $request)
  {
    $category = Apiato::call('Category@FindCategoryByIdAction', [$request]);

    // ..
  }

  /**
   * Create entity (show UI)
   *
   * @param CreateCategoryRequest $request
   */
  public function create(CreateCategoryRequest $request)
  {
    // ..
  }

  /**
   * Add a new entity
   *
   * @param StoreCategoryRequest $request
   */
  public function store(StoreCategoryRequest $request)
  {
    $category = Apiato::call('Category@CreateCategoryAction', [$request]);

    // ..
  }

  /**
   * Edit entity (show UI)
   *
   * @param EditCategoryRequest $request
   */
  public function edit(EditCategoryRequest $request)
  {
    $category = Apiato::call('Category@GetCategoryByIdAction', [$request]);

    // ..
  }

  /**
   * Update a given entity
   *
   * @param UpdateCategoryRequest $request
   */
  public function update(UpdateCategoryRequest $request)
  {
    $category = Apiato::call('Category@UpdateCategoryAction', [$request]);

    // ..
  }

  /**
   * Delete a given entity
   *
   * @param DeleteCategoryRequest $request
   */
  public function delete(DeleteCategoryRequest $request)
  {
    $result = Apiato::call('Category@DeleteCategoryAction', [$request]);

    // ..
  }


    public function adminIndex(GetAllCategoriesRequest $request)
    {
        $role = \Auth::user()->roles;
        $data['menu'] = Apiato::call('AdminMenu@GetAllAdminMenusAction', [$request]);
        $data['title'] = "Додати товар";
        $data['keywords'] = "Ukrainian industry platform";
        $data['description'] = "Ukrainian industry platform";
        $data['categories']=\App\Containers\Site\Models\ProductCategory::where('parent_id',0)
            ->orderBy('position')->get();
        return view('category::admin.index', $data);
    }



    public function show_subcat(GetAllCategoriesRequest $request){
        $id_cat=$request->input('id_cat');
        $is_user=$request->input('is_user');
        $data=$this->print_subcat($id_cat);
        //var_dump($data);
        //проверка для кейса когда нужно вывести конкретные категории по текущему юзеру
        if($is_user==1 && $data['message']=='success'){

            if(Auth::guard('admin')->user()){
                $user=Auth::guard('admin')->user()->id;
                $user_categories= Site_categories::where('user_id', $user)
                    ->get();
                foreach($data['value'] as $category){
                    if (in_array($category->id, unserialize($user_categories[0]->categories))) {
                        $_data['categories'][] = $category;
                    }
                }
            }
            else{
                foreach($data['value'] as $category){
                    $is_children=\App\Category::where('parent_id',$category->id)->get();

                    if(count($is_children)>0){
                        $_data['categories'][] = $category;
                    }

                }
            }
            //var_dump($data);

            $data['value']=(isset($_data['categories'])) ? $_data['categories'] : null;
        }
        //var_dump($data);
        if($data['value']){return json_encode($data);}else{return json_encode('stop');}

    }


    public function show_subcat_all_levels(GetAllCategoriesRequest $request){
        $id_cat=$request->input('id_cat');


        $data=$this->get_all_subcats($id_cat,0);


        $arrOut = $this->makeSingleArray($data);


        $parrent_cats=$this->get_all_parent_cats($id_cat);
        if($parrent_cats){
            foreach($parrent_cats as $value){
                $temp=\DB::table('product_categories')->where('id',$value)->get();
                if(!empty($arrOut)){

                    array_push($arrOut,(array)$temp[0]);
                }
                else{

                    $arrOut[0]=(array)$temp[0];
                }

            }
        }

        return json_encode($arrOut);
    }

    //ToDo:: make one function from show_subcat_levels && show_subcat_levels_back
    public function show_subcat_all_levels_back(GetAllCategoriesRequest $request){
        $id_cat=$request->input('id_cat');


        $data=$this->get_all_subcats($id_cat,0);


        $arrOut = $this->makeSingleArray($data);
        $children_cats=$this->get_all_children_cats($id_cat);
        if($children_cats){
            return json_encode($children_cats);}
    }


    public function print_subcat($id_cat){

        //find all categories which have parent_id = id_cat
        $data['value']=\DB::table('product_categories')->where('parent_id', $id_cat)->orderBy('position')->get();
        $data['message']='success';
        if(count($data['value'])<1){
            return 'Дочерних категорий не обнаружено';
  /*          $data['value']['id']=$id_cat;
            $name='';

            $data['value']['info']=$this->recursive_cat_names($name,$id_cat);
            $data['message']='null';*/
        }
        return $data;
    }

    public function recursive_cat_names($name,$id_cat){
        $this->parent_num=$this->parent_num+1;
        $data['parent']=\DB::table('product_categories')->where('id', $id_cat)->get();
        if($data['parent'][0]->parent_id==0){

            $_name=$data['parent'][0]->name.' > '.$name;
            $this->cat_name=$_name;

        }
        else{
            $id_cat=$data['parent'][0]->parent_id;
            if($name!==''){
                $name=$data['parent'][0]->name.' > '.$name;
            }
            else{
                $name=$data['parent'][0]->name;
            }
            $this->recursive_cat_names($name,$id_cat);
        }


        if(isset($this->cat_name)){
            $cat_info['name']=$this->cat_name;
            $cat_info['parent_num']=$this->parent_num;
            return $cat_info;
        }

    }

    private function get_all_subcats($id_cat,$level){
        //рекурсивно
        //Выбрать из базы рекурсивно все дочерние элементы
        $temp=\DB::table('product_categories')->where('parent_id', $id_cat)->get();
        if(count($temp)>0){
            array_push($this->all_subcats,(array)$temp);
            foreach($temp as $value){
                //$level++;var_dump($level);var_dump('value',$value->id);
                if($temperary=$this->get_all_subcats($value->id,$level)!==false){
                    if(is_object($temperary)){
                        array_push($this->all_subcats,(array)$temperary );}
                }
                else{

                    return $this->all_subcats;
                }
            }
        }
        else{
            return false;
        }



    }

    public function changeCatName(GetAllCategoriesRequest $request){
        $data=[
            'link'=>$request->input('link'),
            'name'=>$request->input('name'),
            'icon'=>$request->input('icon')

        ];
        if($request->input('action')=='add'){
            $last=\App\Containers\Site\Models\ProductCategory::where('parent_id',$request->input('parent_id'))->orderBy('position','desc')->first();
            $data['parent_id']=$request->input('parent_id');
            if(!$last){
                $data['position']=1;
            }
            else{
                $data['position']=$last->position+1;
            }
            \App\Containers\Site\Models\ProductCategory::insert($data);
        }
        else{
        \App\Containers\Site\Models\ProductCategory::where('id',$request->input('id'))->update($data);}
        $cat=\App\Containers\Site\Models\ProductCategory::where('id',$request->input('id'))->first();
        return json_encode(['message'=>'success','cat'=>json_decode($cat)]);
    }

    public function deleteCat(GetAllCategoriesRequest $request){
        $result=\App\Containers\Site\Models\ProductCategory::where('id',$request->input('id_cat'))->first();
        \App\Containers\Site\Models\ProductCategory::where('id',$request->input('id_cat'))->delete();
        return \Response::json($result);

    }

    public function saveRootCatPhotoToSession(GetAllCategoriesRequest $request){
        \Session::forget('temp_root_cat_photo_filename');
        $fileName=$this->base64ToImage($request->input('root_cat_photo'),'root_cat_photos');
        \Session::put('temp_root_cat_photo_filename',$fileName);

    }

    function base64ToImage($imageData,$entity='logos'){
        $data = 'data:image/png;base64,AAAFBfj42Pj4';
        list($type, $imageData) = explode(';', $imageData);
        list(,$extension) = explode('/',$type);
        list(,$imageData)      = explode(',', $imageData);
        $fileName = uniqid().'.'.$extension;
        $imageData = base64_decode($imageData);
        file_put_contents(storage_path('/app/public/'.$entity.'/'.$fileName), $imageData);
        return $fileName;
    }

    public function updateOrCreate(array $attributes, array $values): ?Model
    {

    }


    public function postSaveRootCatPhoto(GetAllCategoriesRequest $request){
        var_dump('REQUEST',$request->input());
        $companyLogo['values']=['photo'=> \Session::get('temp_root_cat_photo_filename')];
        $entityClass=\App\Containers\Site\Models\ProductCategory::class;
        $companyLogo['attributes']['id']=(null!=($request->input('id')) && !empty($request->input('id'))) ? $request->input('id') : null;
        return call_user_func("{$entityClass}::query")->updateOrCreate($companyLogo['attributes'], $companyLogo['values']);
       // Company::updateCompanyRootCatPhoto($companyLogo);

    }

    public function setPosition(GetAllCategoriesRequest $request)
    {
        $update=[];
        $data['category'] = \App\Containers\Site\Models\ProductCategory::where('parent_id',$request->input('parent_id'))->where('id',$request->input('category'))->first();
        if($request->input('state')==0){

            $update['position']=$data['category']->position+1;
            $data['category'] = \App\Containers\Site\Models\ProductCategory::where('parent_id',$request->input('parent_id'))->where('position',$update['position'])->update(['position'=>$update['position']-1]);
        }
        else{
            $update['position']=$data['category']->position-1;
            $data['category'] = \App\Containers\Site\Models\ProductCategory::where('parent_id',$request->input('parent_id'))->where('position',$update['position'])->update(['position'=>$update['position']+1]);
        }

        \App\Containers\Site\Models\ProductCategory::where('parent_id',$request->input('parent_id'))->where('id',$request->input('category'))->update($update);

        return json_encode(['status'=>'success']);
    }

    public function showCatsMainLevel(GetAllCategoriesRequest $request){
        $data['categories']=\App\Containers\Site\Models\ProductCategory::where('parent_id',0)
            ->orderBy('position')->get();
        return view('category::admin.table', $data);

    }

    public function showSubCatsMainDepends(GetAllCategoriesRequest $request){

        $id_cat=$request->input('id_cat');
        $is_user=$request->input('is_user');
        $data=$this->print_subcat($id_cat);
        //var_dump($data);
        //if($data['value']){return json_encode($data);}else{return json_encode('stop');}

if(!is_array($data)){$sata['parent_id']=$id_cat;return view('category::admin.tableSubCatClear', $sata);}
        return view('category::admin.tableSubCat', $data);
    }

}
