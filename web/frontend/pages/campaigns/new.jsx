import {
  BlockStack,
  Box,
  Button,
  Card,
  DatePicker,
  Divider,
  Grid,
  Icon,
  InlineStack,
  LegacyCard,
  Page,
  Select,
  Text,
  TextField,
} from "@shopify/polaris";
import React, { useCallback, useState } from "react";
import { useNavigate } from "react-router-dom";
import { SendIcon } from "@shopify/polaris-icons";
import { CKEditor } from "@ckeditor/ckeditor5-react";
import {
  AccessibilityHelp,
  Alignment,
  AutoLink,
  Autosave,
  Bold,
  Essentials,
  Indent,
  IndentBlock,
  Italic,
  Underline,
  Paragraph,
  SelectAll,
  Undo,
  Link,
  List,
  ClassicEditor,
} from "ckeditor5";
import Placeholder from "../../components/providers/placeholder/placeholder";
import "../../assets/ckeditorStyles/ckeditor5-content.css";
import "../../assets/ckeditorStyles/ckeditor5-editor.css";
import "../../assets/ckeditorStyles/ckeditor5.css";

function CreateCampaign() {
  const navigate = useNavigate();
  const [textFieldValue, setTextFieldValue] = useState("");
  const [handleScheduleDate, setHandleScheduleDate] = useState(false);
  const [selected, setSelected] = useState("Segment_1");
  const [message, setMessage] = useState(
    `<p>안녕하세요 #(name).</p><p>서울웹디자인 데모쇼핑몰에 고객님의 주문이 접수되었습니다.</p><p>&nbsp;</p><p>주문번호: #(orderid)</p><p>주문일시: #(datetime)</p><p>상품: #(products)</p><p>결제 금액: #ordertotal)</p><p>결제 방법: #paymentmethod)</p><p>&nbsp;</p><p>감사합니다.#(shopname)</p>`,
  );

  console.log("messagemessage", message);

  const [{ month, year }, setDate] = useState({ month: 1, year: 2018 });

  const handleTextFieldChange = useCallback((value) => setTextFieldValue(value), []);

  const handleSelectChange = useCallback((value) => setSelected(value), []);

  const options = [
    { label: "Segment 1 (0)", value: "Segment_1" },
    { label: "All customers (0)", value: "All_customers" },
  ];

  const [selectedHour, setSelectedHour] = useState("0");

  const handleSelectChangeHour = useCallback((value) => setSelectedHour(value), []);

  const optionsHours = [];

  for (let i = 0; i <= 23; i++) {
    const label = i.toString().padStart(2, "0"); // Convert the number to a two-digit string
    optionsHours.push({ label, value: i.toString() });
  }

  const [selectedMinuts, setSelectedMinuts] = useState("0");

  const handleSelectChangeMinuts = useCallback((value) => setSelectedMinuts(value), []);

  const optionsMinuts = [];

  for (let i = 0; i <= 59; i++) {
    const label = i.toString().padStart(2, "0"); // Convert the number to a two-digit string
    optionsMinuts.push({ label, value: i.toString() });
  }

  const handleSchedule = () => {
    setHandleScheduleDate((handleScheduleDate) => !handleScheduleDate);
  };

  const [selectedDates, setSelectedDates] = useState({
    start: new Date("Wed Feb 07 2018 00:00:00 GMT-0500 (EST)"),
    end: new Date("Mon Mar 12 2018 00:00:00 GMT-0500 (EST)"),
  });

  const handleMonthChange = useCallback((month, year) => setDate({ month, year }), []);

  const editorConfig = {
    toolbar: {
      items: ["bold", "italic", "underline", "alignment", "|", "link", "|", "outdent", "indent", "placeholder"],
      shouldNotGroupWhenFull: true,
    },

    plugins: [
      AccessibilityHelp,
      Alignment,
      AutoLink,
      Autosave,
      Bold,
      Underline,
      Essentials,
      List,
      Indent,
      IndentBlock,
      Italic,
      Paragraph,
      SelectAll,
      Undo,
      Link,
      Placeholder,
    ],
    fontFamily: {
      supportAllValues: true,
    },
    fontSize: {
      options: [10, 12, 14, "default", 18, 20, 22, 24, 26, 32],
      supportAllValues: true,
    },
    heading: {
      options: [
        {
          model: "paragraph",
          title: "Paragraph",
          class: "ck-heading_paragraph",
        },
        {
          model: "heading1",
          view: "h1",
          title: "Heading 1",
          class: "ck-heading_heading1",
        },
        {
          model: "heading2",
          view: "h2",
          title: "Heading 2",
          class: "ck-heading_heading2",
        },
        {
          model: "heading3",
          view: "h3",
          title: "Heading 3",
          class: "ck-heading_heading3",
        },
        {
          model: "heading4",
          view: "h4",
          title: "Heading 4",
          class: "ck-heading_heading4",
        },
        {
          model: "heading5",
          view: "h5",
          title: "Heading 5",
          class: "ck-heading_heading5",
        },
        {
          model: "heading6",
          view: "h6",
          title: "Heading 6",
          class: "ck-heading_heading6",
        },
      ],
    },
    link: {
      addTargetToExternalLinks: true,
      defaultProtocol: "https://",
      decorators: {
        toggleDownloadable: {
          mode: "manual",
          label: "Downloadable",
          attributes: {
            download: "file",
          },
        },
      },
    },
    placeholderConfig: {
      types: [
        {
          label: "product title",
          value: "product_title",
        },
        {
          label: "store name",
          value: "store_name",
        },
        {
          label: "tracking number",
          value: "tracking_number",
        },
        {
          label: "carrier name",
          value: "carrier_name",
        },
        {
          label: "carrier contact",
          value: "carrier_contact",
        },
        {
          label: "dest. carrier name",
          value: "dest_carrier_name",
        },
        {
          label: "dest. contact NO",
          value: "dest_contact_no",
        },
        {
          label: "shipment status",
          value: "shipment_status",
        },
        {
          label: "latest tracking info",
          value: "latest_tracking_info",
        },
        {
          label: "latest update time",
          value: "latest_update_time",
        },
        {
          label: "tracking link",
          value: "tracking_link",
        },
        {
          label: "transit time",
          value: "transit_time",
        },
        {
          label: "customer first name",
          value: "customer_first_name",
        },
        {
          label: "customer full name",
          value: "customer_full_name",
        },
        {
          label: "order ID",
          value: "order_id",
        },
        {
          label: "comment",
          value: "comment",
        },
        {
          label: "estimated delivery date",
          value: "estimated_delivery_date",
        },
      ], // ADDED
    },
  };

  return (
    <Page
      backAction={{
        content: "Products",
        onAction: () => navigate("/campaigns"),
      }}
      title="Order created"
      primaryAction={{
        content: "Save",
        disabled: true,
      }}
      secondaryActions={[
        {
          content: (
            <span
              style={{
                display: "flex",
                alignItems: "center",
                gap: "10px",
              }}
            >
              <input
                id={`toggle-${1}`}
                type="checkbox"
                name="status"
                className="tgl tgl-light"
                // checked={addressValidatorStatus}
                // onChange={handleAddressValidatorStatus}
              />
              <label htmlFor={`toggle-${1}`} className="tgl-btn"></label>

              <Text>Active/Inactive</Text>
            </span>
          ),
        },
      ]}
    >
      <div className="flex items-start pb-4">
        <div className="w-full">
          <Box padding="200">
            <BlockStack gap="400">
              <Card>
                <BlockStack gap="200">
                  <Text variant="headingMd" as="h2" fontWeight="semibold">
                    Name
                  </Text>
                  <TextField value={textFieldValue} onChange={handleTextFieldChange} error="The name can not be empty" autoComplete="off" />
                </BlockStack>
              </Card>
              <Card>
                <BlockStack gap="200">
                  <Text variant="headingMd" as="h2" fontWeight="semibold">
                    Profile
                  </Text>
                  <Select
                    options={options}
                    onChange={handleSelectChange}
                    value={selected}
                    helpText={`Select the segment you want to send the campaign to.`}
                  />
                </BlockStack>
              </Card>
              <Card>
                <BlockStack gap="200">
                  <Text variant="headingMd" as="h2" fontWeight="semibold">
                    Sender name
                  </Text>
                  <TextField value="SMS" disabled autoComplete="off" helpText="You can set the sender name here." />
                </BlockStack>
              </Card>
              <Card>
                <BlockStack gap={"200"}>
                  <Text as="h2" variant="headingSm">
                    Message
                  </Text>
                  <CKEditor
                    editor={ClassicEditor}
                    config={editorConfig}
                    disabled
                    data={message}
                    onChange={(event, editor) => {
                      const data = editor.getData();
                      setMessage(data);
                    }}
                  />
                </BlockStack>
              </Card>
              <Card>
                <BlockStack gap="400">
                  <Grid columns={{ xs: 2, sm: 2, md: 2, lg: 2, xl: 2 }}>
                    <Grid.Cell>
                      <InlineStack align="start">
                        <Text variant="headingMd" as="h2" fontWeight="semibold">
                          Schedule for later
                        </Text>
                      </InlineStack>
                    </Grid.Cell>
                    <Grid.Cell>
                      <InlineStack align="end">
                        <button
                          onClick={handleSchedule}
                          className={`${
                            handleScheduleDate ? "bg-[var(--p-color-bg-fill-brand-active)]" : "bg-[var(--p-color-bg-fill-brand-disabled)]"
                          } relative inline-flex h-6 w-11 items-center rounded-full`}
                          aria-checked={handleScheduleDate}
                          data-headlessui-state={handleScheduleDate ? "checked" : ""}
                        >
                          <span
                            className={`${
                              handleScheduleDate ? "translate-x-6" : "translate-x-1"
                            } inline-block h-4 w-4 transform rounded-full bg-white`}
                          ></span>
                        </button>
                      </InlineStack>
                    </Grid.Cell>
                  </Grid>
                  {handleScheduleDate && (
                    <>
                      <DatePicker
                        month={month}
                        year={year}
                        onChange={setSelectedDates}
                        onMonthChange={handleMonthChange}
                        selected={selectedDates}
                        multiMonth
                      />
                      <Divider />
                      <Grid columns={{ xs: 2, sm: 2, md: 2, lg: 2, xl: 2 }}>
                        <Grid.Cell>
                          <Select label="Hour" options={optionsHours} onChange={handleSelectChangeHour} value={selectedHour} />
                        </Grid.Cell>
                        <Grid.Cell>
                          <Select label="Minuts" options={optionsMinuts} onChange={handleSelectChangeMinuts} value={selectedMinuts} />
                        </Grid.Cell>
                      </Grid>
                    </>
                  )}
                </BlockStack>
              </Card>
            </BlockStack>
          </Box>
        </div>
        <div className="sticky top-6">
          <div className="ml-4 hidden w-60 flex-none flex-col md:flex lg:ml-4 py-2">
            <div className="relative flex h-[480px] w-full flex-col overflow-hidden rounded-[32px] border-8 border-stone-950 bg-white">
              <div className="absolute left-1/2 top-0 z-10 h-4 w-28 -translate-x-1/2 rounded-b-xl bg-stone-950">
                <div className="absolute left-1/2 top-1 h-1 w-7 -translate-x-1/2 rounded-full  bg-stone-500"></div>
                <div className="absolute right-6 top-0 h-2.5 w-2.5 rounded-full border-2 border-stone-800 bg-stone-900"></div>
              </div>
              <div className="sticky left-0 top-0 flex h-14 w-full items-center justify-center truncate border-b border-stone-200 bg-stone-100 px-2 pt-4 text-sm font-bold">
                SMS
              </div>
              <div className="flex h-full flex-col overflow-y-auto scrollbar scrollbar-thumb-gray-900 scrollbar-track-gray-100">
                <div className="w-full px-2 py-3 text-center text-xs text-stone-500">Message Preview</div>
                <div className="border-3 mx-2 w-fit flex-none break-all rounded-r-xl rounded-t-xl bg-stone-200  p-3 text-xs">
                  <div dangerouslySetInnerHTML={{ __html: message }} />
                </div>
              </div>
            </div>
            <div class="pt-6">
              <Button fullWidth icon={<Icon source={SendIcon} />}>
                Send Test Message
              </Button>
            </div>
          </div>
        </div>
      </div>
    </Page>
  );
}

export default CreateCampaign;
