<?php 

class Product_model extends MY_Model {


    /**
     * 获取所有产品分类
     * @param $mchId 商户编号
     * @deprecated please use getCategories instead.
     */
    public function get_category($mchId) {
        return $this->getCategories($mchId);
    }

	/**
	 * 获取所有产品分类
	 * @param $mchId 商户编号
	 */
	public function getCategories($mchId) {
		$categories = $this->db->where('mchId',$mchId)->get('categories')->result();
        $output = [];
        $this->dealCategoryList($categories, -1, 0, $output);
        return $output;
	}

    /**
	 * 获取单个分类
	 * @param $categoryId 产品分类编号
	 */
	public function get_category_by_id($categoryId) {
		return $this->db->where('id', $categoryId)->get('categories')->row();
	}

	/**
	 * 添加分类
	 *@data 数据
	 */
	public function add_category($data) {
		$this->db->insert('categories',$data);
		return $this->db->insert_id();
	}

	/**
	 * 修改分类
	 *@where 数据
	 *@data 数据
	 */
	public function update_category($where,$data) {
		return $this->db->where($where)->update('categories',$data);
	}

	/**
	 * 查询分类子分类
	 *@id 数据id
	 */
	public function sub_category_num($id) {
		return $this->db->where('parentCategoryId',$id)->count_all_results('categories');
	}

	/**
	 * 删除分类
	 *@id 数据id
	 */
	public function del_category($id) {
		return $this->db->delete('categories',['id'=>$id]);
	}

    /**
	 * 获取所有产品
	 *@mchId 商户ID
	 */
	public function get_product($mchId) {
		return $this->db->where('mchId',$mchId)->order_by('id','DESC')->get('products')->result();
	}

    /**
	 * 删除产品
	 *@id 数据id
	 */
	public function del_product($id) {
		return $this->db->delete('products',['id'=>$id]);
	}

    /**
     * 添加产品
     * @data array 数据
     */
    public function add_product($data){
        $this->db->insert('products',$data);
        return $this->db->insert_id();
    }

    /**
	 * 获取单个产品
	 *@id 产品ID
	 */
	public function get_by_id($id) {
		return $this->db->where('id',$id)->get('products')->row();
	}
    
    /**
	 * 获取分类下产品
	 *@id 分类ID
	 */
	public function get_by_category($id) {
		return $this->db->where('categoryId',$id)->get('products')->result();
	}

     /**
     * 修改产品
     * @id 产品id
     * @data array 数据
     */
    public function update_product($id,$data){
        return $this->db->where('id',$id)->update('products',$data);
    }
    public function get_mch_products($mch_id){
        return $this->db->where('mchId',$mch_id)->get('products')->result();
    }


    private function dealCategoryList($categories, $parentId, $level, array &$output) {
    	$level++;
        foreach ($categories as $i => $category) {
            if ($category->parentCategoryId == $parentId) {
                $category->level = $level;
                $output[] = $category;
                unset($categories[$i]); // 从数据中移除已经确定level的产品分类
                // 递归调用处理
                $this->dealCategoryList($categories, $category->id, $level, $output);
            }
        }
    }
}
