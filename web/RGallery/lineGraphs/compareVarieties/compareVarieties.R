
#NDVI variable URI = "http://www.opensilex.org/demo/id/variables/v002"

library(shiny)
library(phisWSClientR)
library(openssl)
library(vegawidget)
library(dplyr)
library(gam)

ui <- fluidPage(
    fluidRow(
        #Connection interface
        column(4, textInput('userName', '', value = 'Enter Username')),
        column(4, textInput('password', '', value = 'Enter password')),
        column(4, actionButton("connect", "Connect"))
    ),
    fluidRow(
        sidebarPanel(
            #Variable choice
            uiOutput('variety1_ui'),
            uiOutput('variety2_ui'),
            actionButton("updateData", "Update Data"),
            actionButton("updateChart", "Update Chart")),
        mainPanel(
            column(1),
            column(11, vegawidget::vegawidgetOutput("chart"))

        )),
    fluidRow(
        verbatimTextOutput("data_in")
    )
)

chart <- as_vegaspec(list(
    `$schema` = vega_schema(),
    title = "Leaf Area Index of two plant varieties",
    width = 500,
    height = 500,
    autosize = list(
        type = "fit",
        contains = "padding"
    ),
    data = list(name = "dataSet"),
    scales = list(
        list(
            name = "x",
            type = "date",
            range = "width",
            domain = list(
                data = "dataSet",
                field = "date"
            )
        ),
        list(
            name = "y",
            type = "linear",
            range = "height",
            domain = list(
                data = "dataSet",
                field = "value1"
            )
        )
    ),
    layer = list(
        list(
            mark = list(
                type = "point",
                color = "MidnightBlue"
            ),
            encoding = list(
                x = list(
                    title = "Date",
                    field = "date",
                    type = "temporal"
                ),
                y = list(
                    title = "Leaf Area Index",
                    field = "fit1",
                    type = "quantitative"
                )
            )
        ),
        list(
            mark = list(
                type = "point",
                color = "Maroon"
            ),
            #############     Tooltip for nearest value ##########
            #        selection = list(
            #                detail = list(
            #                    type = "single",
            #                    on = "mouseover",
            #                    nearest = TRUE
            #                )
            #            ),
            ######################################################
            encoding = list(
                x = list(
                    field = "date",
                    type = "temporal"
                ),
                y = list(
                    field = "fit2",
                    type = "quantitative"
                )
            )
        ),
        list(
            mark = list(
                type = "line",
                color = "Blue"
            ),
            encoding = list(
                x = list(
                    field = "date",
                    type = "temporal"
                ),
                y = list(
                    field = "value1",
                    type = "quantitative"
                )
            )
        ),
        list(
            mark = list(
                type = "line",
                color = "Red"
            ),
            encoding = list(
                x = list(
                    field = "date",
                    type = "temporal"
                ),
                y = list(
                    field = "value2",
                    type = "quantitative"
                )
            )
        ))
))

server <- function(input, output, session) {

    varietyList <- reactiveVal()
    data1 <- reactiveVal()
    data2 <- reactiveVal()
    data <- reactiveVal()

    observeEvent(input$connect, {
        #Connection and token acquisition
        password <- input$password
        connectToOpenSILEXWS(username = "guest@opensilex.org",password = "guest", url = "www.opensilex.org/openSilexAPI/rest/")

        #Variable list acquisition and display
        scientificObjects <- getScientificObjects()
        varietyList(scientificObjects$data)
        varietyList(subset(varietyList(), varietyList()$rdfType =="http://www.opensilex.org/vocabulary/oeso#Plot"))
        variety <- vector()
        for (i in 1:nrow(varietyList())){
            variety[i] <- varietyList()$properties[[i]]$value[(length(varietyList()$properties[[1]]$value))]
        }
        varietyList(transmute(varietyList(), uri = varietyList()$uri, variety = variety))

        output$variety1_ui <- renderUI(
            selectInput('variety1', 'Variety 1', variety)
        )
        output$variety2_ui <- renderUI(
            selectInput('variety2', 'Variety 2', variety)
        )
    })

    # Creation of the Data Table with information of interest
    observeEvent(input$updateData, {
        if(!is.null(varietyList())){
        numVar1 <- match(input$variety1, varietyList()$variety)
        nameUriVar1 <-  varietyList()$uri[numVar1]
        data1(getData(, variableUri = "http://www.opensilex.org/demo/id/variables/v001", objectUri = nameUriVar1)$data)

        numVar2 <- match(input$variety2, varietyList()$variety)
        nameUriVar2 <-  varietyList()$uri[numVar2]
        data2(getData(, variableUri = "http://www.opensilex.org/demo/id/variables/v001", objectUri = nameUriVar2)$data)

        #Temporal data formatting
        data1 <- data1()
        if (is.null(data1())==FALSE){
            data1$date <- as.POSIXct(strptime(data1$date, "%Y-%m-%dT%H:%M:%S+0200"))
            data1(select(data1, date, value))}
        data2 <- data2()
        if (is.null(data2())==FALSE){
            data2$date <- as.POSIXct(strptime(data2$date, "%Y-%m-%dT%H:%M:%S+0200"))
            data2(select(data2, date, value))}

        #browser()
        #GAM modeling of first variety
        dat1 <- as.POSIXlt(data1$date)$yday
        dta1 <- as.data.frame(cbind(date = dat1, value = data1$value))
        dta1$date <- dta1$date - dta1$date[nrow(dta1)]

        date1.loess = gam(value~lo(date,span=0.2),data=dta1)
        seq.date = seq(0,64,length=50)
        fit1 = predict(date1.loess,newdata=data.frame(date=seq.date))
        fit1 = sort(fit1, decreasing = TRUE)

        #GAM modeling of second variety
        dat2 <- as.POSIXlt(data2$date)$yday
        dta2 <- as.data.frame(cbind(date = dat2, value = data2$value))
        dta2$date <- dta2$date - dta2$date[nrow(dta2)]

        date2.loess = gam(value~lo(date,span=0.2),data=dta2)
        fit2 = predict(date2.loess,newdata=data.frame(date=seq.date))
        fit2 = sort(fit2, decreasing = TRUE)

        data(inner_join(data1, data2, by = "date"))
        data(cbind(data(),fit1,fit2))
        data(select(data(), date, value1 = value.x, value2 = value.y, fit1 = fit1, fit2 = fit2))
        }
    })


    # whenever update button is press, the chart will be updated
    vw_shiny_set_data("chart", name = "dataSet", value = data(), run = FALSE)
    vw_shiny_run("chart", value = input$updateChart, ignoreNULL = TRUE)

    #Plot and data table
    output$chart <- vegawidget::renderVegawidget(chart)
    output$data_in <- renderPrint(data())
}

shinyApp(ui, server)
