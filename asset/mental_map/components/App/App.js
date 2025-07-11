import React, {createContext, useContext, useEffect, useReducer, useState} from 'react';
import './App.css'
import AppLoader from "./AppLoader";
import Editor from "../Editor";
import api, {parseError} from "../../Api";
import MentalMapReducer from "../../Lib/MentalMapReducer";
import ImagesReducer from "../../Lib/ImagesReducer";
import TreeView from "../TreeView";
import Toolbar from "../Toolbar";
import {formatTextWithLineNumbers} from "../../Lib";
import PlanTreeView from "../PlanTreeView";

export const MentalMapContext = createContext({});
export const ImagesContext = createContext({});
export const SchedulesContext = createContext({});

export default function App({mentalMapId}) {
  console.log('app')

  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [state, dispatch] = useReducer(MentalMapReducer, {})
  const [imagesState, imagesDispatch] = useReducer(ImagesReducer, {})
  const [schedules, setSchedules] = useState([])
  const [isTreeView, setIsTreeView] = useState(false)
  const [formattedMapText, setFormattedMapText] = useState()

  useEffect(() => {
    api
      .get(`/admin/index.php?r=mental-map/get&id=${mentalMapId}`)
      .then((response) => {
        setLoading(false);
        dispatch({
          type: 'mental_map_loaded',
          mentalMap: response.mentalMap
        })
        imagesDispatch({
          type: 'images_loaded',
          images: response.images
        })
        setSchedules(response.schedules)
        setIsTreeView(Boolean(response?.mentalMap?.treeView))
        setFormattedMapText(formatTextWithLineNumbers(response.mentalMap?.text))
      })
      .catch(async (error) => setError(await parseError(error)))
  }, [])

  const mentalMapContext = {state, dispatch}
  const imagesContext = {state: imagesState, dispatch: imagesDispatch}

  return (
    <div>
      {loading
        ? <AppLoader/>
        : (
          <MentalMapContext.Provider value={mentalMapContext}>
            <div>
              <Toolbar
                mentalMapId={mentalMapId}
                title={state.name}
                schedules={schedules}
                setFormattedMapText={setFormattedMapText}
              />
            </div>
            {isTreeView
              ? state?.settings?.planTreeView ? <PlanTreeView texts={formattedMapText}/> : <TreeView texts={formattedMapText}/>
              : <ImagesContext.Provider value={imagesContext}>
                {<Editor/>}
              </ImagesContext.Provider>}
          </MentalMapContext.Provider>
        )
      }
    </div>
  )
}

export function useMentalMap() {
  return useContext(MentalMapContext);
}

export function useImages() {
  return useContext(ImagesContext);
}
