<?php

class SewadarsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
        $file_Path = Yii::getPathOfAlias('webroot.attachments.sewadars_images');

		$model=new Sewadars;
		$model->serial_no = basic::getLatestSewadarSerialNumber();
        $basic = new basic();
        $age = $basic->getAgeRange();
        $area = $basic->getAreas();
        $sections = $basic->getSewadarSections();
        $bloodGroup = basic::getBloodGroup();
		if (isset($_POST['Sewadars'])) {

            $model->attributes=$_POST['Sewadars'];
            $model->blood_group=$_POST['Sewadars']['blood_group'];
            $model->sewardar_picture = CUploadedFile::getInstance($model,'sewardar_picture');
            if ($model->sewardar_picture) {
                $ext = pathinfo($model->sewardar_picture, PATHINFO_EXTENSION);
                $SName = basic::removeSpace($model->sewadar_name);
                $FileName =  $model->badge_no.'_'.$SName.'.'.$ext;
                $saved = $model->sewardar_picture->saveAs($file_Path.'/'.$FileName);
                $model->sewardar_picture = $FileName;
            } else {
                $saved = true;
                $model->sewardar_picture = '';
            }

            if($model->validate()) {
                if($model->save())
                    if($saved) {
                        Yii::app()->user->setFlash('success', 'Record Inserted Successfully.');
                    }else {
                        Yii::app()->user->setFlash('error', 'error while uploading file.');
                    }
                    $this->refresh();
                }
		}

		$this->render('create',array(
			'model'=>$model,
			'age'=>$age,
			'area'=>$area,
			'sections'=>$sections,
			'bloodGroup'=>$bloodGroup,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
        $file_Path = Yii::getPathOfAlias('webroot.attachments.sewadars_images');
		$model=$this->loadModel($id);
        $basic = new basic();
        $age = $basic->getAgeRange();
        $area = $basic->getAreas();
        $sections = $basic->getSewadarSections();
        $bloodGroup = basic::getBloodGroup();
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Sewadars'])) {
			$model->attributes=$_POST['Sewadars'];
            $model->blood_group=$_POST['Sewadars']['blood_group'];
            $model->sewardar_picture = CUploadedFile::getInstance($model,'sewardar_picture');
            if ($model->sewardar_picture) {
                $ext = pathinfo($model->sewardar_picture, PATHINFO_EXTENSION);
                $SName = basic::removeSpace($model->sewadar_name);
                $FileName =  $model->badge_no.'_'.$SName.'.'.$ext;
                $saved = $model->sewardar_picture->saveAs($file_Path.'/'.$FileName);
                $model->sewardar_picture = $FileName;
            } else {
                $saved = true;
                $model->sewardar_picture = '';
            }

            if($model->validate()) {
                if($model->save())
                    if($saved) {
                        Yii::app()->user->setFlash('success', 'Record Inserted Successfully.');
                    }else {
                        Yii::app()->user->setFlash('error', 'error while uploading file.');
                    }
                $this->refresh();
            }
		}
        $this->render('update',array(
            'model'=>$model,
            'age'=>$age,
            'area'=>$area,
            'sections'=>$sections,
            'bloodGroup'=>$bloodGroup,
        ));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Sewadars');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
        $basic = new basic();
        $sections = $basic->getSewadarSections();
        $age = $basic->getAgeRange();
        $bloodGroup = basic::getBloodGroup();

		$model=new Sewadars('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Sewadars']))
			$model->attributes=$_GET['Sewadars'];

		$this->render('admin',array(
			'model'=>$model,
			'sections'=>$sections,
			'age'=>$age,
			'bloodGroup'=>$bloodGroup,

		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Sewadars::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='sewadars-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
