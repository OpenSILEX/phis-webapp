## functions to transform a data.frame in a spatial data object and visualize it

#' @param indata a dataframe of field data
#' @details indata must contain a 'position' column of each polygon of the field
#' @return a new data.frame, in long format, with the position in 2 columns longitude and latitude
coordData<-function(indata){
  temp1<-indata
  
  # I retrieve the coordinates to format the dataframe in spatial polygon object
  # I work on the coord column containing the coordinates of the polygon by plotid
  #temp1$coords0<-gsub('{"type":"Polygon","coordinates":[[[',"",indata$coord)
  #temp1$coords0<-gsub("]]]}","",temp1$coords0,perl=TRUE)
  temp1$coords0<-substring(indata$coord,first=36)
  temp1$coords0<-substr(temp1$coords0,1,nchar(temp1$coords0)-4)
  temp1$coords0<-gsub(", ",",",temp1$coords0,perl=TRUE)
  temp1<-separate(temp1,coords0,c("poly1","poly2","poly3","poly4","poly5"),sep="\\],\\[",remove=FALSE)
  temp1<-separate(temp1,poly1,c("x1","y1"),sep=",",remove=TRUE)
  temp1<-separate(temp1,poly2,c("x2","y2"),sep=",",remove=TRUE)
  temp1<-separate(temp1,poly3,c("x3","y3"),sep=",",remove=TRUE)
  temp1<-separate(temp1,poly4,c("x4","y4"),sep=",",remove=TRUE)
  temp1<-separate(temp1,poly5,c("x5","y5"),sep=",",remove=TRUE)
  toto<-gather(temp1,key="polyx","longitude",x1,x2,x3,x4,x5)
  titi<-gather(temp1,key="polyy","latitude",y1,y2,y3,y4,y5)
  
  # by plotid, I've got 5 lines in my dataset to draw the polygon
  temp2<-cbind.data.frame(select(toto,-coords0,-polyx,-y1,-y2,-y3,-y4,-y5),select(titi,latitude))
  if ("Day" %in% names(temp2)){
    temp2<-arrange(temp2,plotid,Day)
  } else {
    temp2<-arrange(temp2,plotid)
  }
  temp2$longitude<-as.numeric(temp2$longitude)
  temp2$latitude<-as.numeric(temp2$latitude)
  return(temp2)
}


#' @param indata a data frame
#' @param incoorddata a dataframe returned by coordData() function (5 lines by ident: polygon)
#' @details requests leaflet library
#' @return a spatialPolygonDataFrame to use with leaflet and so on...
spatialData<-function(indata,incoorddata){
  # Step 1: transform in list to help transforming in a spatial object
  tmp1<-split(incoorddata[,c("longitude","latitude")],incoorddata$plotid)
  ps<-lapply(tmp1,Polygon)
  p1<-lapply(seq_along(ps),function(i) Polygons(list(ps[[i]]),
                                                ID=names(tmp1[i])))
  # Step 2: transform in a spatial polygon object
  tmp2<-SpatialPolygons(p1,proj4string = CRS("+init=epsg:4326"))
  # transform to give a spatial polygon dataframe
  tmp3<-SpatialPolygonsDataFrame(tmp2,data.frame(plotid = unique(indata$plotid),row.names = unique(indata$plotid),indata))
  return(tmp3)
}  

#' @title a graphical function for a field experiment using leaflet
#' @param inPF SpatialPolygonsDataFrame object of the diaphen platform
#' @param inEnvir SpatialPointsDataFrame object of the environnemental
#' @param inDesign SpatialPolygonsDataFrame object of the experimental design
#' @param inParam character, the name of the visualised parameter
#' @param colorType character, a name for RcolorBrewer of color (Greens, Blues, Reds...)
#' @param inTitle character, a title for the produced graphic
#' @return a leaflet graphic
graphFocus<-function(inPF,inDesign,inParam,colorType,inTitle){
  xzoom1<-mean(inDesign@"bbox"[1,])
  yzoom1<-mean(inDesign@"bbox"[2,])
  # a homemade popup!
  inDesign@data[,"mypopup"]<-paste0("Experiment: ",inDesign@data[,"Experiment"],"<br>",
                                    "genotype: ",inDesign@data[,"Geno"],
                                    ", plotid: ",inDesign@data[,"plotid"],
                                    ",<br>scenario-rep: ",inDesign@data[,"scenarrep"])
  
  if (is.numeric(inDesign@data[,inParam])){
    tempcolor <- colorNumeric(palette = colorType,domain = inDesign@data[,inParam])
  } else {
    tempcolor <- colorFactor(palette = colorType,domain = inDesign@data[,inParam],
                             n=length(unique(inDesign@data[,inParam])))
  }
  
  m1<-leaflet(inPF) %>% addTiles() %>% setView(xzoom1,yzoom1, zoom=18) %>% 
    addPolygons(stroke = FALSE, fillColor = "red") %>% addProviderTiles(providers$OpenStreetMap) %>%
    addPolygons(data=inDesign,stroke = FALSE, fillColor = ~tempcolor(inDesign@data[,inParam]),opacity = 1,
                fillOpacity = 1,smoothFactor=0.2,popup = ~as.character(mypopup),
                highlightOptions = highlightOptions(color = "white", weight = 2,bringToFront=TRUE)) %>%
    #addMarkers(data=mySensor3,lng=~longitude, lat=~latitude, popup = ~as.character(name), label = ~as.character(name)) %>%
    addLegend("bottomright", pal = tempcolor, values = ~inDesign@data[,inParam],opacity = 1,
              title=inTitle)
  m1
}

#-------------------- end of file -----------------------------------------

