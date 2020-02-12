#-------------------------------------------------------------------------------
# Program: plotVarDemo.R
# Objective: draw graph of environment variable over time
# Authors: Chourrout Elise
# Creation: 15/02/2019
# Update: 11/03/2019 (Jean-Eudes Hollebecq)
#-------------------------------------------------------------------------------

#' @title Plot Environmental Data
#' @description Demonstration function
#' @importFrom magrittr %>%
#' @importFrom plotly layout
#' @importFrom plotly plot_ly
#' @importFrom plotly add_trace
#'
#' @param varURI uri of the variable to plot from the \code{\link{variableList}} function or the web service directly
#' @param token a token from \code{\link{getToken}} function
#' @param wsUrl url of the webservice

#' @examples
#' \donttest{
#'  initializeClientConnection(apiID="ws_private", url = "www.opensilex.org/openSilexAPI/rest/")
#'  aToken <- getToken("guest@opensilex.org","guest")
#'  token <- aToken$data
#'  vars <- variableList(token = token)
#'  vars
#'  plotVar(vars$value[1], token = token)
#' }
#'
#' @export

plotVarDemo <- function(varURI, token, wsUrl = "www.opensilex.org/openSilexAPI/rest/"){
  phisWSClientR::initializeClientConnection(apiID="ws_private", url = wsUrl)

  ### Collecting Data
  variableList <- variableList(token = token, url = wsUrl)
  ## Data
  Data <- list()
  Data = lapply(varURI,FUN = function(uri){
    enviroData <- getDataVar(varURI = uri, variableList = variableList, token = token)$enviroData
    yVar <- enviroData$value
    # Casting Date in the right format
    xVar <- as.POSIXct(enviroData$date, tz = "UTC", format = "%Y-%m-%dT%H:%M:%S")
    DataX <- data.frame(date = xVar, value = yVar)
    return(DataX)
  })
  variableList <- variableList[which(variableList$uri %in% varURI), ]

  ### Plotting
  ## Theme
  # Color Palette
  colorVar <- list("#7CB5EC", "#0F528A", "#003152", "#577A003")
  colorFill <- colorVar
  for (i in 1:length(colorVar)){
    colorFill[i] <- paste(colorFill[i], "4D", sep = "")
  }
  colorBgHover <- "#F8F8F8"
  colorText <- "#525252"
  # Labels and grid
  y <- list(title = paste('<b>', variableList[1,"name"], ' (',variableList[1,"unity"], ')' , '</b>', sep = ""), color = '#282828',
            tickfont = list(family = 'serif'), gridwidth = 2)
  x <- list(title = '<b>Date</b>', tickfont = list(family = 'serif'), gridwidth = 2)
  title <- list(size = 20, color = '#282828', tickfont = list(family = 'serif'))

  ## Plot
  p <- plotly::plot_ly()
  # Backgound creation
  p <- plotly::layout(p, xaxis = x, yaxis = y,
                      titlefont = title,
                      margin = list(l = 60, r = 70, t = 70, b =  60))
  for (i in 1:(length(Data))){
    # Markers and Lines formatting
    nameY <- paste('y', i, sep = "")
    marker <- NULL
    marker$color <- as.character(colorVar[i])
    hoverlabel <- list(bgcolor = colorBgHover, font = list(color = colorText), hoveron = "")
    hoverlabel$bordercolor <- as.character(colorVar[i])
    # Values of the graph
    yVar <- Data[[i]]$value

    # Screening of the values without smoothing as lines
    p <- plotly::add_lines(p, x = Data[[i]]$date, y = yVar, line = list(color = as.character(colorVar[i])), name = variableList[i,"method"], yaxis = nameY, hoverlabel = hoverlabel,
                           text = ~paste(Data[[i]]$date, '<br>', variableList[i,"acronym"], ': <b>', yVar, variableList[i,"unity"], '</b>'), hoverinfo = 'text')
    }


  ## Labels
  if (length(varURI) == 1){
    p <- plotly::layout(p, title = paste('<b>Tendency of ', variableList[1,"name"], '</b><br><i>', variableList[1,"method"], '</i>' , sep = ""))
  } else if (i == 2) {
    y <- list(title = paste('<b>', variableList[2, "name"], ' (', variableList[2, "unity"], ')' , '</b>', sep = ""), color = '#282828', showgrid = FALSE,
              gridwidth = 2,  tickfont = list(family = 'serif'), overlaying = "y", side = "right")
    p <- plotly::layout(p, yaxis2 = y)
    p <- plotly::layout(p, title = "<b>Tendency of environmental variables among time</br>")
  } else {
    y <- list(title = paste('<b>', variableList[2, "name"], ' (', variableList[2, "unity"], ')' , '</b>', sep = ""), color = '#282828', showgrid = FALSE,
              gridwidth = 2,  tickfont = list(family = 'serif'), overlaying = "y", side = "right")
    p <- plotly::layout(p, yaxis = y)
    p <- plotly::layout(p, title = "<b>Tendency of environmental variables among time</br>")
  }
  p
  # Creation of the html object to screen in the variablesStudy
  # print(plotly::plotly_json(p))
  htmlwidgets::saveWidget(p, "plotWidget.html", selfcontained = FALSE)
  # htmlwidgets::
  # jsonlite::write_json(plotly::plotly_json(p), "plotlySchema")
  # jsonlite::write_json(jsonlite::fromJSON(plotly::plotly_json(p)), "plotlyData")
  # jsonlite::write_json(Data,"gridData")
  #return(Data)
}
